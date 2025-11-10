<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
