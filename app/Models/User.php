<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const PROTECTED_EMAIL = 'contato@innsystem.com.br';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isProtected(): bool
    {
        return strcasecmp((string) $this->email, self::PROTECTED_EMAIL) === 0;
    }

    public function canBeManagedBy(?self $actor): bool
    {
        if (! $actor) {
            return false;
        }

        if (! $this->isProtected()) {
            return true;
        }

        return strcasecmp((string) $actor->email, self::PROTECTED_EMAIL) === 0;
    }
}
