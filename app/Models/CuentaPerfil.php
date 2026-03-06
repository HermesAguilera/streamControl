<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaPerfil extends Model
{
    use HasFactory;

    protected $table = 'cuenta_perfiles';

    protected $fillable = [
        'cuenta_id',
        'numero_perfil',
        'pin',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $cuentaPerfil): void {
            $cuentaPerfil->syncPinToPerfiles();
        });

        static::deleted(function (self $cuentaPerfil): void {
            Perfil::query()
                ->where('cuenta_id', $cuentaPerfil->cuenta_id)
                ->where('nombre_perfil', (string) $cuentaPerfil->numero_perfil)
                ->update([
                    'pin' => null,
                    'updated_at' => now(),
                ]);
        });
    }

    private function syncPinToPerfiles(): void
    {
        Perfil::query()
            ->where('cuenta_id', $this->cuenta_id)
            ->where('nombre_perfil', (string) $this->numero_perfil)
            ->update([
                'pin' => $this->pin,
                'updated_at' => now(),
            ]);
    }

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class);
    }
}
