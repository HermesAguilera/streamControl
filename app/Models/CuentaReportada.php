<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaReportada extends Model
{
    use HasFactory;

    protected $table = 'cuentas_reportadas';

    protected $fillable = [
        'perfil_id',
        'cuenta_id',
        'plataforma_id',
        'cuenta',
        'numero_perfil',
        'descripcion',
        'estado',
        'solucionado_at',
        'reportado_por',
    ];

    protected $casts = [
        'solucionado_at' => 'datetime',
    ];

    public function perfil(): BelongsTo
    {
        return $this->belongsTo(Perfil::class);
    }

    public function cuentaOrigen(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }

    public function plataforma(): BelongsTo
    {
        return $this->belongsTo(Plataforma::class);
    }

    public function reportadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reportado_por');
    }

    public function markAsSolved(): void
    {
        $this->update([
            'estado' => 'solucionado',
            'solucionado_at' => now(),
        ]);
    }
}
