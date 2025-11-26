<?php

namespace App\Jobs;

use App\Services\GoogleBooksService;
use App\Models\Livro;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncActiveBooksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 minutos
    public $tries = 2;

    /**
     * Execute the job.
     */
    public function handle(GoogleBooksService $googleBooksService): void
    {
        Log::info('Iniciando sincronização de livros ativos...');

        $syncedCount = 0;
        $errorCount = 0;

        // Obter livros acessados nos últimos 30 dias
        $activeBooks = Livro::whereNotNull('last_accessed_at')
            ->where('last_accessed_at', '>', now()->subDays(30))
            ->orderBy('last_accessed_at', 'desc')
            ->limit(100) // Limitar a top 100 mais recentes
            ->get();

        Log::info("Encontrados {$activeBooks->count()} livros ativos para sincronizar");

        foreach ($activeBooks as $livro) {
            try {
                // Extrair Google ID da bibliografia
                $googleId = $this->extractGoogleId($livro->bibliografia);

                if (!$googleId) {
                    Log::warning("Livro sem Google ID, pulando", ['livro_id' => $livro->id]);
                    continue;
                }

                // Buscar dados atualizados da API
                $bookData = $googleBooksService->getBookById($googleId);

                if (!$bookData) {
                    Log::warning("Não foi possível obter dados da API", [
                        'livro_id' => $livro->id,
                        'google_id' => $googleId,
                    ]);
                    $errorCount++;
                    continue;
                }

                // Atualizar apenas campos que podem mudar
                $updated = false;

                // Atualizar preço se diferente
                if (isset($bookData['price']) && $bookData['price'] != $livro->preco) {
                    $livro->preco = $bookData['price'];
                    $updated = true;
                    Log::info("Preço atualizado", [
                        'livro_id' => $livro->id,
                        'old_price' => $livro->preco,
                        'new_price' => $bookData['price'],
                    ]);
                }

                // Atualizar descrição se vazia ou muito curta
                if (isset($bookData['description']) &&
                    (empty($livro->bibliografia) || strlen($livro->bibliografia) < 50)) {
                    $livro->bibliografia = $this->formatBibliografia($bookData);
                    $updated = true;
                }

                // Atualizar capa se não existir
                if (isset($bookData['thumbnail']) && empty($livro->imagem_capa)) {
                    $imagePath = $this->downloadThumbnail($bookData['thumbnail'], $livro->isbn);
                    if ($imagePath) {
                        $livro->imagem_capa = $imagePath;
                        $updated = true;
                    }
                }

                if ($updated) {
                    $livro->save();
                    $syncedCount++;
                }

                // Rate limiting
                usleep(200000); // 0.2 segundos entre cada livro

            } catch (\Exception $e) {
                Log::error('Erro ao sincronizar livro', [
                    'livro_id' => $livro->id,
                    'error' => $e->getMessage(),
                ]);
                $errorCount++;
            }
        }

        Log::info('Sincronização de livros ativos concluída', [
            'total_processed' => $activeBooks->count(),
            'synced' => $syncedCount,
            'errors' => $errorCount,
        ]);
    }

    /**
     * Extrai o Google Books ID da bibliografia
     */
    private function extractGoogleId(?string $bibliografia): ?string
    {
        if (!$bibliografia) {
            return null;
        }

        // Procurar por "Google Books ID: XXXXX"
        if (preg_match('/Google Books ID:\s*([a-zA-Z0-9_-]+)/', $bibliografia, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Download da thumbnail
     */
    private function downloadThumbnail(string $url, string $isbn): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);

            if ($response->successful()) {
                $extension = 'jpg';
                $filename = 'livros/' . \Illuminate\Support\Str::slug($isbn) . '-' . time() . '.' . $extension;

                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $response->body());

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
        Log::error('SyncActiveBooksJob falhou', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
