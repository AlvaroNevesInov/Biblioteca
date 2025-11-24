<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleBooksService
{
    private string $baseUrl = 'https://www.googleapis.com/books/v1';
    private ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google_books.api_key');
    }

    /**
     * Pesquisa livros na Google Books API
     *
     * @param string $query Termo de pesquisa
     * @param int $maxResults Número máximo de resultados (1-40)
     * @param int $startIndex Índice inicial para paginação
     * @return array
     */
    public function search(string $query, int $maxResults = 20, int $startIndex = 0): array
    {
        try {
            $params = [
                'q' => $query,
                'maxResults' => min($maxResults, 40),
                'startIndex' => $startIndex,
                'langRestrict' => 'pt', // Priorizar resultados em português
            ];

            if ($this->apiKey) {
                $params['key'] = $this->apiKey;
            }

            $response = Http::timeout(10)->get("{$this->baseUrl}/volumes", $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'totalItems' => $data['totalItems'] ?? 0,
                    'items' => $this->processBooks($data['items'] ?? []),
                ];
            }

            Log::error('Erro na pesquisa Google Books API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao pesquisar livros: ' . $response->status(),
                'totalItems' => 0,
                'items' => [],
            ];
        } catch (\Exception $e) {
            Log::error('Exceção na pesquisa Google Books API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao conectar com a API: ' . $e->getMessage(),
                'totalItems' => 0,
                'items' => [],
            ];
        }
    }

    /**
     * Obtém detalhes de um livro específico pelo ID do Google Books
     *
     * @param string $volumeId ID do volume no Google Books
     * @return array|null
     */
    public function getBookById(string $volumeId): ?array
    {
        try {
            $params = [];
            if ($this->apiKey) {
                $params['key'] = $this->apiKey;
            }

            $response = Http::timeout(10)->get("{$this->baseUrl}/volumes/{$volumeId}", $params);

            if ($response->successful()) {
                $data = $response->json();
                return $this->processBook($data);
            }

            Log::error('Erro ao obter livro por ID', [
                'volumeId' => $volumeId,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao obter livro por ID', [
                'volumeId' => $volumeId,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Processa múltiplos livros da resposta da API
     *
     * @param array $items
     * @return array
     */
    private function processBooks(array $items): array
    {
        return array_map(fn($item) => $this->processBook($item), $items);
    }

    /**
     * Processa um único livro e normaliza os dados
     *
     * @param array $item
     * @return array
     */
    private function processBook(array $item): array
    {
        $volumeInfo = $item['volumeInfo'] ?? [];
        $saleInfo = $item['saleInfo'] ?? [];

        // Obter ISBNs disponíveis
        $isbn13 = null;
        $isbn10 = null;

        if (isset($volumeInfo['industryIdentifiers'])) {
            foreach ($volumeInfo['industryIdentifiers'] as $identifier) {
                if ($identifier['type'] === 'ISBN_13') {
                    $isbn13 = $identifier['identifier'];
                }
                if ($identifier['type'] === 'ISBN_10') {
                    $isbn10 = $identifier['identifier'];
                }
            }
        }

        // Priorizar ISBN-13, senão usar ISBN-10
        $isbn = $isbn13 ?? $isbn10;

        // Obter autores
        $authors = $volumeInfo['authors'] ?? [];

        // Obter editora
        $publisher = $volumeInfo['publisher'] ?? null;

        // Obter imagem de capa (priorizar maior resolução)
        $thumbnail = null;
        if (isset($volumeInfo['imageLinks'])) {
            $thumbnail = $volumeInfo['imageLinks']['large']
                ?? $volumeInfo['imageLinks']['medium']
                ?? $volumeInfo['imageLinks']['small']
                ?? $volumeInfo['imageLinks']['thumbnail']
                ?? $volumeInfo['imageLinks']['smallThumbnail']
                ?? null;

            // Converter para HTTPS
            if ($thumbnail) {
                $thumbnail = str_replace('http://', 'https://', $thumbnail);
            }
        }

        // Obter preço
        $price = null;
        if (isset($saleInfo['listPrice']['amount'])) {
            $price = $saleInfo['listPrice']['amount'];
        } elseif (isset($saleInfo['retailPrice']['amount'])) {
            $price = $saleInfo['retailPrice']['amount'];
        }

        // Obter descrição
        $description = $volumeInfo['description'] ?? null;

        return [
            'google_id' => $item['id'] ?? null,
            'title' => $volumeInfo['title'] ?? 'Sem título',
            'subtitle' => $volumeInfo['subtitle'] ?? null,
            'authors' => $authors,
            'publisher' => $publisher,
            'published_date' => $volumeInfo['publishedDate'] ?? null,
            'description' => $description,
            'isbn' => $isbn,
            'isbn_13' => $isbn13,
            'isbn_10' => $isbn10,
            'page_count' => $volumeInfo['pageCount'] ?? null,
            'categories' => $volumeInfo['categories'] ?? [],
            'language' => $volumeInfo['language'] ?? null,
            'thumbnail' => $thumbnail,
            'price' => $price,
            'preview_link' => $volumeInfo['previewLink'] ?? null,
            'info_link' => $volumeInfo['infoLink'] ?? null,
        ];
    }

    /**
     * Pesquisa por ISBN específico
     *
     * @param string $isbn
     * @return array|null
     */
    public function searchByIsbn(string $isbn): ?array
    {
        $result = $this->search("isbn:{$isbn}", 1);

        if ($result['success'] && !empty($result['items'])) {
            return $result['items'][0];
        }

        return null;
    }

    /**
     * Pesquisa por título e autor
     *
     * @param string $title
     * @param string|null $author
     * @return array
     */
    public function searchByTitleAndAuthor(string $title, ?string $author = null): array
    {
        $query = "intitle:{$title}";

        if ($author) {
            $query .= "+inauthor:{$author}";
        }

        return $this->search($query);
    }
}
