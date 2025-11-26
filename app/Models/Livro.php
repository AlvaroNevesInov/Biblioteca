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
}
