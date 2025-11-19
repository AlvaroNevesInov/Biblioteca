<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisicao extends Model
{
    use HasFactory;

    protected $table = 'requisicoes';

    protected $fillable = [
        'user_id',
        'livro_id',
        'foto_cidadao',
        'estado',
        'data_requisicao',
        'data_prevista_devolucao',
        'data_devolucao',
        'observacoes',
    ];

    protected $casts = [
        'data_requisicao' => 'date',
        'data_prevista_devolucao' => 'date',
        'data_devolucao' => 'date',
    ];

    /**
     * Uma requisição pertence a um utilizador
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Uma requisição pertence a um livro
     */
    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    /**
     * Verificar se a requisição está pendente
     */
    public function isPendente(): bool
    {
        return $this->estado === 'pendente';
    }

    /**
     * Verificar se a requisição está aprovada
     */
    public function isAprovada(): bool
    {
        return $this->estado === 'aprovada';
    }

    /**
     * Verificar se a requisição está rejeitada
     */
    public function isRejeitada(): bool
    {
        return $this->estado === 'rejeitada';
    }

    /**
     * Verificar se a requisição está devolvida
     */
    public function isDevolvida(): bool
    {
        return $this->estado === 'devolvida';
    }

    /**
     * Verificar se a requisição está ativa (pendente ou aprovada)
     */
    public function isAtiva(): bool
    {
        return in_array($this->estado, ['pendente', 'aprovada']);
    }

    /**
     * Scope para requisições ativas
     */
    public function scopeAtivas($query)
    {
        return $query->whereIn('estado', ['pendente', 'aprovada']);
    }

    /**
     * Scope para requisições de um utilizador específico
     */
    public function scopeDoUtilizador($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para requisições de um livro específico
     */
    public function scopeDoLivro($query, $livroId)
    {
        return $query->where('livro_id', $livroId);
    }

    /**

     * Scope para requisições passadas (devolvidas ou rejeitadas)

     */

    public function scopePassadas($query)

    {

        return $query->whereIn('estado', ['devolvida', 'rejeitada']);

    }


    /**

     * Scope para ordenar por data de requisição decrescente

     */

    public function scopeRecentes($query)

    {

        return $query->orderBy('data_requisicao', 'desc');

    }
}
