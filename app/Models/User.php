<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    public const ROLES = ['administrador', 'secretaria', 'doctor'];

    protected $fillable = ['usuario', 'password', 'rol'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function secretaria(): HasOne
    {
        return $this->hasOne(Secretaria::class);
    }

    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }

    public function esSecretaria(): bool
    {
        return $this->rol === 'secretaria';
    }

    public function esDoctor(): bool
    {
        return $this->rol === 'doctor';
    }

    public function nombreVisible(): string
    {
        return match ($this->rol) {
            'doctor' => $this->doctor?->nombre_completo ?? $this->usuario,
            'secretaria' => $this->secretaria?->nombre_completo ?? $this->usuario,
            default => 'Administrador',
        };
    }
}
