<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plataforma extends Model
{
    use HasFactory;

    protected $table = 'plataformas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activa',
        'perfiles_por_cuenta',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'perfiles_por_cuenta' => 'integer',
    ];

    public function perfiles(): HasMany
    {
        return $this->hasMany(Perfil::class);
    }

    public function cuentas(): HasMany
    {
        return $this->hasMany(Cuenta::class);
    }
}
