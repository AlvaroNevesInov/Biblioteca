<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Livro extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'nome',
        'editora_id',
        'bibliografia',
        'imagem_capa',
        'preco',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'last_accessed_at' => 'datetime',
    ];

    /**
     * Um livro pertence a uma editora
     */
    public function editora(): BelongsTo
    {
        return $this->belongsTo(Editora::class);
    }

    /**
     * Um livro pode ter muitos autores
     */
    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_livro')
                    ->withTimestamps();
    }

    /**
     * Um livro pode ter muitas requisições
     */
    public function requisicoes(): HasMany
    {
        return $this->hasMany(Requisicao::class);
    }

    /**
     * Um livro pode ter muitos reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Obter reviews ativos do livro
     */
    public function reviewsAtivos()
    {
        return $this->reviews()->where('estado', 'ativo')->with('user')->orderBy('created_at', 'desc');
    }

    /**
     * Verificar se o livro está disponível para requisição
     */
    public function estaDisponivel(): bool
    {
        // Verifica se não existe nenhuma requisição ativa (pendente ou aprovada)
        return !$this->requisicoes()
            ->whereIn('estado', ['pendente', 'aprovada'])
            ->exists();
    }

    /**
     * Obter a requisição ativa do livro (se existir)
     */
    public function requisicaoAtiva()
    {
        return $this->requisicoes()
            ->whereIn('estado', ['pendente', 'aprovada'])
            ->first();
    }

    /**
     * Obter livros relacionados baseado na similaridade da bibliografia
     *
     * @param int $limit Número máximo de livros relacionados a retornar
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function livrosRelacionados(int $limit = 5)
    {
        // Se não tem bibliografia, retornar livros da mesma editora ou autores
        if (empty($this->bibliografia)) {
            return $this->livrosRelacionadosPorMetadata($limit);
        }

        // Obter todos os outros livros que tenham bibliografia
        $outrosLivros = self::where('id', '!=', $this->id)
            ->whereNotNull('bibliografia')
            ->where('bibliografia', '!=', '')
            ->with(['editora', 'autores'])
            ->get();

        if ($outrosLivros->isEmpty()) {
            return collect();
        }

        // Calcular similaridade para cada livro
        $livrosComSimilaridade = $outrosLivros->map(function ($livro) {
            $similaridade = $this->calcularSimilaridade($this->bibliografia, $livro->bibliografia);

            // Bonus se compartilham autores
            $autoresComum = $this->autores->pluck('id')->intersect($livro->autores->pluck('id'));
            if ($autoresComum->isNotEmpty()) {
                $similaridade += 0.15; // Adiciona 15% de similaridade
            }

            // Bonus se compartilham editora
            if ($this->editora_id === $livro->editora_id) {
                $similaridade += 0.05; // Adiciona 5% de similaridade
            }

            $livro->similaridade = min($similaridade, 1.0); // Limitar a 1.0
            return $livro;
        });

        // Ordenar por similaridade e retornar os mais similares
        return $livrosComSimilaridade
            ->filter(function ($livro) {
                return $livro->similaridade > 0.1; // Apenas livros com similaridade > 10%
            })
            ->sortByDesc('similaridade')
            ->take($limit)
            ->values();
    }

    /**
     * Obter livros relacionados baseado em metadata (editora, autores)
     * quando não há bibliografia disponível
     */
    private function livrosRelacionadosPorMetadata(int $limit)
    {
        $autoresIds = $this->autores->pluck('id');

        // Buscar livros que compartilham autores ou editora
        return self::where('id', '!=', $this->id)
            ->where(function ($query) use ($autoresIds) {
                $query->where('editora_id', $this->editora_id)
                    ->orWhereHas('autores', function ($q) use ($autoresIds) {
                        $q->whereIn('autores.id', $autoresIds);
                    });
            })
            ->with(['editora', 'autores'])
            ->limit($limit)
            ->get();
    }

    /**
     * Calcular similaridade entre duas strings usando técnica de TF-IDF simplificado
     * e Cosine Similarity
     */
    private function calcularSimilaridade(string $texto1, string $texto2): float
    {
        // Normalizar textos
        $tokens1 = $this->tokenizar($texto1);
        $tokens2 = $this->tokenizar($texto2);

        if (empty($tokens1) || empty($tokens2)) {
            return 0.0;
        }

        // Calcular similaridade de Jaccard
        $intersecao = count(array_intersect($tokens1, $tokens2));
        $uniao = count(array_unique(array_merge($tokens1, $tokens2)));

        if ($uniao === 0) {
            return 0.0;
        }

        $jaccardSimilarity = $intersecao / $uniao;

        // Calcular similaridade de cosseno com TF (frequência de termos)
        $tf1 = array_count_values($tokens1);
        $tf2 = array_count_values($tokens2);

        $cosineSimilarity = $this->calcularCosseno($tf1, $tf2);

        // Combinar ambas as métricas (média ponderada)
        return ($jaccardSimilarity * 0.4) + ($cosineSimilarity * 0.6);
    }

    /**
     * Tokenizar texto em palavras, removendo stopwords e normalizando
     */
    private function tokenizar(string $texto): array
    {
        // Converter para minúsculas
        $texto = mb_strtolower($texto, 'UTF-8');

        // Remover caracteres especiais, manter apenas letras e números
        $texto = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $texto);

        // Dividir em palavras
        $palavras = preg_split('/\s+/', $texto, -1, PREG_SPLIT_NO_EMPTY);

        // Stopwords em português
        $stopwords = [
            'a', 'o', 'e', 'é', 'de', 'da', 'do', 'em', 'um', 'uma', 'os', 'as', 'dos', 'das',
            'à', 'ao', 'aos', 'na', 'no', 'nas', 'nos', 'por', 'para', 'com', 'sem', 'sob',
            'que', 'se', 'como', 'mas', 'mais', 'ou', 'quando', 'muito', 'nos', 'já', 'eu',
            'tu', 'ele', 'ela', 'nós', 'vós', 'eles', 'elas', 'meu', 'minha', 'seu', 'sua',
            'este', 'esse', 'aquele', 'esta', 'essa', 'aquela', 'isto', 'isso', 'aquilo',
            'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with',
            'by', 'from', 'up', 'about', 'into', 'through', 'during', 'is', 'are', 'was', 'were'
        ];

        // Filtrar stopwords e palavras muito curtas
        $palavras = array_filter($palavras, function ($palavra) use ($stopwords) {
            return strlen($palavra) > 2 && !in_array($palavra, $stopwords);
        });

        return array_values($palavras);
    }

    /**
     * Calcular similaridade de cosseno entre dois vetores de frequência de termos
     */
    private function calcularCosseno(array $tf1, array $tf2): float
    {
        // Obter todas as palavras únicas
        $todasPalavras = array_unique(array_merge(array_keys($tf1), array_keys($tf2)));

        if (empty($todasPalavras)) {
            return 0.0;
        }

        // Calcular produto escalar e magnitudes
        $produtoEscalar = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        foreach ($todasPalavras as $palavra) {
            $freq1 = $tf1[$palavra] ?? 0;
            $freq2 = $tf2[$palavra] ?? 0;

            $produtoEscalar += $freq1 * $freq2;
            $magnitude1 += $freq1 * $freq1;
            $magnitude2 += $freq2 * $freq2;
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0.0;
        }

        return $produtoEscalar / ($magnitude1 * $magnitude2);
    }
}
