<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;


class Encomenda extends Model
{
    protected $fillable = [
        'user_id',
        'numero_encomenda',
        'nome_completo',
        'email',
        'telefone',
        'morada',
        'cidade',
        'codigo_postal',
        'pais',
        'subtotal',
        'taxas',
        'total',
        'estado',
        'stripe_payment_intent_id',
        'notas'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'taxas' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($encomenda) {
            if (!$encomenda->numero_encomenda) {
                $encomenda->numero_encomenda = 'ENC-' . strtoupper(uniqid());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(EncomendaItem::class);
    }

    public function isPendente()
    {
        return $this->estado === 'pendente';
    }

    public function isPaga()
    {
        return $this->estado === 'paga';
    }

    public function isProcessando()
    {
        return $this->estado === 'processando';
    }

    public function isEnviada()
    {
        return $this->estado === 'enviada';
    }

    public function isEntregue()
    {
        return $this->estado === 'entregue';
    }

    public function isCancelada()
    {
        return $this->estado === 'cancelada';
    }
}
