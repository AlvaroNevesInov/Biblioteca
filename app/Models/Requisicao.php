<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'data_recepcao',
        'recebido_por',
        'observacoes',

    ];

    protected $casts = [

        'data_requisicao' => 'date',
        'data_prevista_devolucao' => 'date',
        'data_devolucao' => 'date',
        'data_recepcao' => 'date',
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
     * Admin que confirmou a recepção do livro
     */

    public function recebidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recebido_por');
    }

    /**
     * Uma requisição pode ter um review
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Verificar se a requisição tem review
     */
    public function hasReview(): bool
    {
        return $this->review()->exists();
    }

    /**
     * Verificar se a requisição pode receber review
     * Apenas requisições devolvidas podem ter review
     */
    public function podeReceberReview(): bool
    {
        return $this->isDevolvida() && !$this->hasReview();
    }

    /**
     * Verificar se a recepção do livro foi confirmada
     */

    public function isRecebido(): bool
    {
        return !is_null($this->data_recepcao);
    }


    /**
     * Calcular o número de dias decorridos desde a requisição até à recepção
     */

    public function diasDecorridos(): ?int
    {
        if (!$this->data_recepcao) {
            return null;
        }
        return $this->data_requisicao->diffInDays($this->data_recepcao);
    }

    /**
     * Calcular dias de atraso (se ultrapassou a data prevista)
     */

    public function diasAtraso(): int
    {
        $dataComparacao = $this->data_recepcao ?? now();

        if ($dataComparacao->gt($this->data_prevista_devolucao)) {
            return $this->data_prevista_devolucao->diffInDays($dataComparacao);
        }
        return 0;
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
