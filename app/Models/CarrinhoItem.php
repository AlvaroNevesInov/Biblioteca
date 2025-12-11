<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarrinhoItem extends Model
{
    protected $fillable = [
        'user_id',
        'livro_id',
        'quantidade',
        'abandoned_cart_email_sent_at'
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'abandoned_cart_email_sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->quantidade * $this->livro->preco;
    }
}
