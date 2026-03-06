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

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class);
    }
}
