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

    protected static function booted(): void
    {
        static::saved(function (self $cuenta): void {
            Perfil::query()
                ->where('cuenta_id', $cuenta->id)
                ->update([
                    'proveedor_nombre' => $cuenta->proveedor,
                    'correo_cuenta' => trim(mb_strtolower((string) $cuenta->correo)),
                    'contrasena_cuenta' => $cuenta->contrasena,
                    'fecha_inicio' => $cuenta->fecha_inicio?->toDateString(),
                    'fecha_corte' => $cuenta->fecha_corte?->toDateString(),
                    'updated_at' => now(),
                ]);
        });
    }

    public function plataforma(): BelongsTo
    {
        return $this->belongsTo(Plataforma::class);
    }

    public function perfiles(): HasMany
    {
        return $this->hasMany(Perfil::class);
    }

    public function configuracionPerfiles(): HasMany
    {
        return $this->hasMany(CuentaPerfil::class)->orderBy('numero_perfil');
    }
}
