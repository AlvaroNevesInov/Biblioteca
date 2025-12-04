<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo_path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Verificar se o utilizador é Admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar se o utilizador é Cidadão
     */
    public function isCidadao(): bool
    {
        return $this->role === 'cidadao';
    }

     /**
     * Um utilizador tem muitas requisições
     */

    public function requisicoes(): HasMany
    {
        return $this->hasMany(Requisicao::class);
    }

    /**
     * Um utilizador tem muitos reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Um utilizador pode ter muitos alertas de disponibilidade
     */
    public function availabilityAlerts(): HasMany
    {
        return $this->hasMany(AvailabilityAlert::class);
    }

    /**
     * Contar requisições ativas do utilizador
     */
    public function contarRequisicoesAtivas(): int
    {
        return $this->requisicoes()->whereIn('estado', ['pendente', 'aprovada'])->count();
    }

    /**
     * Verificar se o utilizador pode requisitar mais livros (máximo 3)
     */
    public function podeRequisitar(): bool
    {
        return $this->contarRequisicoesAtivas() < 3;
    }

    /**
     * Get the URL to the user's profile photo.
     * Sobrescreve o método do Jetstream para usar public/uploads em vez de storage
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->profile_photo_path
                    ? url($this->profile_photo_path)
                    : $this->defaultProfilePhotoUrl();
        });
    }


}
