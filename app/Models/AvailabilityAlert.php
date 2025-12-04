<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilityAlert extends Model
{
    protected $fillable = [
        'user_id',
        'livro_id',
        'notificado',
    ];

    protected $casts = [
        'notificado' => 'boolean',
    ];

    /**
     * Relação com User (cidadão que solicitou alerta)
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
     * Scope para filtrar alertas não notificados
     */
    public function scopeNaoNotificados($query)
    {
        return $query->where('notificado', false);
    }

    /**
     * Scope para filtrar alertas de um livro específico
     */
    public function scopeDoLivro($query, $livroId)
    {
        return $query->where('livro_id', $livroId);
    }

    /**
     * Scope para filtrar alertas de um utilizador específico
     */
    public function scopeDoUtilizador($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Marca o alerta como notificado
     */
    public function marcarComoNotificado(): bool
    {
        return $this->update(['notificado' => true]);
    }
}
