<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Autor extends Model
{
    use HasFactory;

    protected $table = 'autores';

    protected $fillable = [
        'nome',
        'foto',
    ];

    /**
     * Um autor pode ter muitos livros
     */
    public function livros(): BelongsToMany
    {
        return $this->belongsToMany(Livro::class, 'autor_livro')
                    ->withTimestamps();
    }
}
