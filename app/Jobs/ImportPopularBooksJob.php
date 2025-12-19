<?php

namespace App\Jobs;

use App\Services\GoogleBooksService;
use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ImportPopularBooksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hora
    public $tries = 3;

    /**
     * Execute the job.
     */
    public function handle(GoogleBooksService $googleBooksService): void
    {
        Log::info('Iniciando importação de livros populares...');

        // Marcar como em progresso
        Cache::put('popular_books_importing', true, now()->addHour());

        $totalImported = 0;
        $targetBooks = 200;

        // Categorias populares e autores famosos para pesquisar
        $searchQueries = [
            // Clássicos e best-sellers
            'bestseller', 'classic literature', 'fiction', 'non-fiction',

            // Gêneros populares
            'mystery', 'thriller', 'romance', 'science fiction', 'fantasy',
            'biography', 'history', 'self-help', 'business',

            // Autores portugueses e brasileiros famosos
            'José Saramago', 'Fernando Pessoa', 'Eça de Queiroz',
            'Machado de Assis', 'Paulo Coelho', 'Jorge Amado',

            // Autores internacionais famosos
            'Shakespeare', 'Tolkien', 'J.K. Rowling', 'Stephen King',
            'Agatha Christie', 'Dan Brown', 'George Orwell',

            // Séries populares
            'Harry Potter', 'Lord of the Rings', 'Game of Thrones',
            'Sherlock Holmes', 'Percy Jackson',

            // Literatura clássica
            'Dostoevsky', 'Tolstoy', 'Victor Hugo', 'Jane Austen',
            'Charles Dickens', 'Mark Twain',

            // Categorias educacionais
            'science', 'mathematics', 'philosophy', 'psychology',
            'technology', 'programming', 'art', 'music',
        ];

        foreach ($searchQueries as $query) {
            if ($totalImported >= $targetBooks) {
                break;
            }

            try {
                // Pesquisar 40 livros por query (máximo da API)
                $result = $googleBooksService->search($query, 40, 0);

                if ($result['success'] && !empty($result['items'])) {
                    foreach ($result['items'] as $book) {
                        if ($totalImported >= $targetBooks) {
                            break 2;
                        }

                        // Só importar livros com ISBN
                        if (!$book['isbn']) {
                            continue;
                        }

                        // Verificar se já existe
                        if (Livro::where('isbn', $book['isbn'])->exists()) {
                            continue;
                        }

                        try {
                            $this->importBook($book);
                            $totalImported++;

                            // Log a cada 50 livros
                            if ($totalImported % 50 == 0) {
                                Log::info("Importados {$totalImported} livros até agora...");
                            }

                            // Pequeno delay para respeitar rate limits
                            usleep(100000); // 0.1 segundo

                        } catch (\Exception $e) {
                            Log::warning('Erro ao importar livro', [
                                'isbn' => $book['isbn'],
                                'title' => $book['title'],
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }

                // Delay entre queries
                sleep(1);

            } catch (\Exception $e) {
                Log::error('Erro ao pesquisar query: ' . $query, [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Marcar como completo (cache expira em 90 dias para permitir re-sincronização)

        Cache::put('popular_books_imported', true, now()->addDays(90));
        Cache::forget('popular_books_importing');

        Log::info("Importação concluída! Total de livros importados: {$totalImported}");
    }

    /**
     * Importa um livro individual
     */
    private function importBook(array $book): void
    {
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

        // 3. Download da imagem (opcional, pode falhar)
        $imagePath = null;
        if ($book['thumbnail']) {
            try {
                $imagePath = $this->downloadThumbnail($book['thumbnail'], $book['isbn']);
            } catch (\Exception $e) {
                // Continua mesmo se a imagem falhar
            }
        }

        // 4. Criar o livro
        $livro = Livro::create([
            'isbn' => $book['isbn'],
            'nome' => $book['title'],
            'editora_id' => $editora->id,
            'bibliografia' => $this->formatBibliografia($book),
            'imagem_capa' => $imagePath,
            'preco' => $book['price'],
            'stock' => 1,
        ]);

        // 5. Associar os autores
        $livro->autores()->attach($autorIds);
    }

    /**
     * Download da thumbnail
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
            // Silenciosamente falha
        }

        return null;
    }

    /**
     * Formata bibliografia
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
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ImportPopularBooksJob falhou', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        Cache::forget('popular_books_importing');
    }
}
