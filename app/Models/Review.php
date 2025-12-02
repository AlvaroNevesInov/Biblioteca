<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'livro_id',
        'requisicao_id',
        'comentario',
        'estado',
        'justificacao_recusa',
    ];

    /**
     * Relação com User (cidadão que fez o review)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação com Livro
     */
    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    /**
     * Relação com Requisicao
     */
    public function requisicao(): BelongsTo
    {
        return $this->belongsTo(Requisicao::class);
    }

    /**
     * Verifica se o review está suspenso
     */
    public function isSuspenso(): bool
    {
        return $this->estado === 'suspenso';
    }

    /**
     * Verifica se o review está ativo
     */
    public function isAtivo(): bool
    {
        return $this->estado === 'ativo';
    }

    /**
     * Verifica se o review foi recusado
     */
    public function isRecusado(): bool
    {
        return $this->estado === 'recusado';
    }

    /**
     * Scope para filtrar reviews suspensos
     */
    public function scopeSuspensos($query)
    {
        return $query->where('estado', 'suspenso');
    }

    /**
     * Scope para filtrar reviews ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('estado', 'ativo');
    }

    /**
     * Scope para filtrar reviews recusados
     */
    public function scopeRecusados($query)
    {
        return $query->where('estado', 'recusado');
    }

    /**
     * Scope para filtrar reviews de um livro específico
     */
    public function scopeDoLivro($query, $livroId)
    {
        return $query->where('livro_id', $livroId);
    }

    /**
     * Scope para filtrar reviews de um utilizador específico
     */
    public function scopeDoUtilizador($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
