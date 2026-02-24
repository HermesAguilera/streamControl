<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuenta extends Model
{
    use HasFactory;

    protected $table = 'cuentas';

    protected $fillable = [
        'plataforma_id',
        'proveedor',
        'correo',
        'contrasena',
        'fecha_inicio',
        'fecha_corte',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_corte' => 'date',
    ];

    public function plataforma(): BelongsTo
    {
        return $this->belongsTo(Plataforma::class);
    }

    public function perfiles(): HasMany
    {
        return $this->hasMany(Perfil::class);
    }
}
