<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use App\Services\GoogleBooksService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoogleBooksController extends Controller
{
    private GoogleBooksService $googleBooksService;

    public function __construct(GoogleBooksService $googleBooksService)
    {
        $this->googleBooksService = $googleBooksService;
    }

    /**
     * Mostra a página de pesquisa na Google Books API
     */
    public function index()
    {
        return view('google-books.index');
    }

    /**
     * Realiza pesquisa na Google Books API
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255',
            'page' => 'nullable|integer|min:1',
        ]);

        $query = $request->input('query');
        $page = $request->input('page', 1);
        $maxResults = 20;
        $startIndex = ($page - 1) * $maxResults;

        $result = $this->googleBooksService->search($query, $maxResults, $startIndex);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return view('google-books.search-results', [
            'query' => $query,
            'result' => $result,
            'page' => $page,
            'maxResults' => $maxResults,
        ]);
    }

    /**
     * Mostra detalhes de um livro específico da Google Books API
     */
    public function show(string $volumeId)
    {
        $book = $this->googleBooksService->getBookById($volumeId);

        if (!$book) {
            return redirect()->route('google-books.index')
                ->with('error', 'Livro não encontrado na Google Books API.');
        }

        // Verificar se o livro já existe na base de dados
        $existingBook = null;
        if ($book['isbn']) {
            $existingBook = Livro::where('isbn', $book['isbn'])->first();

            // Se existir, registrar acesso para tracking
            if ($existingBook) {
                $existingBook->touch('last_accessed_at');
            }
        }

        return view('google-books.show', [
            'book' => $book,
            'existingBook' => $existingBook,
        ]);
    }

    /**
     * Importa um livro da Google Books API para a base de dados
     */
    public function import(Request $request)
    {
        $request->validate([
            'google_id' => 'required|string',
        ]);

        $book = $this->googleBooksService->getBookById($request->google_id);

        if (!$book) {
            return redirect()->route('google-books.index')
                ->with('error', 'Livro não encontrado na Google Books API.');
        }

        // Validar se tem ISBN
        if (!$book['isbn']) {
            return back()->with('error', 'Este livro não possui ISBN e não pode ser importado.');
        }

        // Verificar se já existe
        if (Livro::where('isbn', $book['isbn'])->exists()) {
            return back()->with('error', 'Este livro já existe na base de dados.');
        }

        try {
            // 1. Criar ou obter a editora
            $editora = null;
            if ($book['publisher']) {
                $editora = Editora::firstOrCreate(
                    ['nome' => $book['publisher']],
                    ['nome' => $book['publisher']]
                );
            } else {
                $editora = Editora::firstOrCreate(
                    ['nome' => 'Desconhecida'],
                    ['nome' => 'Desconhecida']
                );
            }

            // 2. Criar ou obter os autores
            $autorIds = [];
            if (!empty($book['authors'])) {
                foreach ($book['authors'] as $authorName) {
                    $autor = Autor::firstOrCreate(
                        ['nome' => $authorName],
                        ['nome' => $authorName]
                    );
                    $autorIds[] = $autor->id;
                }
            } else {
                $autor = Autor::firstOrCreate(
                    ['nome' => 'Desconhecido'],
                    ['nome' => 'Desconhecido']
                );
                $autorIds[] = $autor->id;
            }

            // 3. Fazer download da imagem de capa
            $imagePath = null;
            if ($book['thumbnail']) {
                $imagePath = $this->downloadThumbnail($book['thumbnail'], $book['isbn']);
            }

            // 4. Criar o livro
            $livro = Livro::create([
                'isbn' => $book['isbn'],
                'nome' => $book['title'],
                'editora_id' => $editora->id,
                'bibliografia' => $this->formatBibliografia($book),
                'imagem_capa' => $imagePath,
                'preco' => $book['price'],
            ]);

            // 5. Associar os autores
            $livro->autores()->attach($autorIds);

            return redirect()->route('livros.show', $livro)
                ->with('success', 'Livro importado com sucesso da Google Books!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao importar livro: ' . $e->getMessage());
        }
    }

    /**
     * Faz download da thumbnail e salva no storage
     */
    private function downloadThumbnail(string $url, string $isbn): ?string
    {
        try {
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $extension = 'jpg';
                $filename = 'livros/' . Str::slug($isbn) . '-' . time() . '.' . $extension;

                Storage::disk('public')->put($filename, $response->body());

                return $filename;
            }
        } catch (\Exception $e) {
            // Log mas não falha a importação se a imagem falhar
            Log::warning('Falha ao fazer download da thumbnail', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Formata a bibliografia com informações do livro
     */
    private function formatBibliografia(array $book): string
    {
        $parts = [];

        if ($book['description']) {
            $parts[] = $book['description'];
        }

        $metadata = [];

        if ($book['published_date']) {
            $metadata[] = "Data de Publicação: {$book['published_date']}";
        }

        if ($book['page_count']) {
            $metadata[] = "Páginas: {$book['page_count']}";
        }

        if ($book['language']) {
            $metadata[] = "Idioma: " . strtoupper($book['language']);
        }

        if (!empty($book['categories'])) {
            $metadata[] = "Categorias: " . implode(', ', $book['categories']);
        }

        if (!empty($metadata)) {
            $parts[] = "\n\n" . implode("\n", $metadata);
        }

        if ($book['google_id']) {
            $parts[] = "\n\nGoogle Books ID: {$book['google_id']}";
        }

        return implode("\n", $parts);
    }

    /**
     * Importa um livro e redireciona para criar requisição
     * Usado quando o usuário quer requisitar um livro da API que ainda não está no catálogo
     */
    public function importAndRequest(Request $request)
    {
        $request->validate([
            'google_id' => 'required|string',
        ]);

        $book = $this->googleBooksService->getBookById($request->google_id);

        if (!$book) {
            return back()->with('error', 'Livro não encontrado na Google Books API.');
        }

        // Validar se tem ISBN
        if (!$book['isbn']) {
            return back()->with('error', 'Este livro não possui ISBN e não pode ser importado.');
        }

        // Verificar se já existe
        $existingLivro = Livro::where('isbn', $book['isbn'])->first();

        if ($existingLivro) {
            // Se já existe, redireciona para criar requisição
            return redirect()->route('requisicoes.create', ['livro_id' => $existingLivro->id])
                ->with('success', 'Este livro já existe no catálogo. Pode requisitá-lo agora!');
        }

        try {
            // 1. Criar ou obter a editora
            $editora = null;
            if ($book['publisher']) {
                $editora = Editora::firstOrCreate(
                    ['nome' => $book['publisher']],
                    ['nome' => $book['publisher']]
                );
            } else {
                $editora = Editora::firstOrCreate(
                    ['nome' => 'Desconhecida'],
                    ['nome' => 'Desconhecida']
                );
            }

            // 2. Criar ou obter os autores
            $autorIds = [];
            if (!empty($book['authors'])) {
                foreach ($book['authors'] as $authorName) {
                    $autor = Autor::firstOrCreate(
                        ['nome' => $authorName],
                        ['nome' => $authorName]
                    );
                    $autorIds[] = $autor->id;
                }
            } else {
                $autor = Autor::firstOrCreate(
                    ['nome' => 'Desconhecido'],
                    ['nome' => 'Desconhecido']
                );
                $autorIds[] = $autor->id;
            }

            // 3. Fazer download da imagem de capa
            $imagePath = null;
            if ($book['thumbnail']) {
                $imagePath = $this->downloadThumbnail($book['thumbnail'], $book['isbn']);
            }

            // 4. Criar o livro
            $livro = Livro::create([
                'isbn' => $book['isbn'],
                'nome' => $book['title'],
                'editora_id' => $editora->id,
                'bibliografia' => $this->formatBibliografia($book),
                'imagem_capa' => $imagePath,
                'preco' => $book['price'],
            ]);

            // 5. Associar os autores
            $livro->autores()->attach($autorIds);

            // 6. Redirecionar para criar requisição
            return redirect()->route('requisicoes.create', ['livro_id' => $livro->id])
                ->with('success', 'Livro importado com sucesso! Preencha os dados para completar a requisição.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao importar livro: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint para pesquisa rápida (usado em autocomplete, etc)
     */
    public function apiSearch(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $result = $this->googleBooksService->search($request->input('q'), 10);

        return response()->json($result);
    }

    /**
     * Pesquisa por ISBN
     */
    public function searchByIsbn(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string',
        ]);

        $book = $this->googleBooksService->searchByIsbn($request->input('isbn'));

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livro não encontrado com este ISBN.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'book' => $book,
        ]);
    }
}
