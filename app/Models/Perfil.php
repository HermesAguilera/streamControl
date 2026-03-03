<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perfil extends Model
{
    use HasFactory;

    protected $table = 'perfiles';

    protected $fillable = [
        'plataforma_id',
        'cuenta_id',
        'nombre_perfil',
        'pin',
        'cliente_nombre',
        'cliente_telefono',
        'proveedor_nombre',
        'correo_cuenta',
        'contrasena_cuenta',
        'fecha_inicio',
        'fecha_corte',
        'fecha_caducidad_cuenta',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_corte' => 'date',
        'fecha_caducidad_cuenta' => 'date',
    ];

    public function setNombrePerfilAttribute($value): void
    {
        $value = (string) $value;

        if (preg_match('/^(?:perfil\s*[-_]\s*)?(\d+)$/i', trim($value), $matches)) {
            $this->attributes['nombre_perfil'] = (string) ((int) $matches[1]);

            return;
        }

        $this->attributes['nombre_perfil'] = trim($value);
    }

    public function plataforma(): BelongsTo
    {
        return $this->belongsTo(Plataforma::class);
    }

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class);
    }

    public function getDiasRestantesAttribute(): ?int
    {
        if (!$this->fecha_caducidad_cuenta) {
            return null;
        }

        return Carbon::today()->diffInDays(Carbon::parse($this->fecha_caducidad_cuenta), false);
    }
}
