<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    /** @use HasFactory<\Database\Factories\EmpresaFactory> */
    use HasFactory, SoftDeletes; // Usa SoftDeletes si lo tienes en tu migración

    protected $table = 'empresas'; // Asegúrate de que el modelo esté asociado a la tabla 'empresas'

    protected $fillable = [
        'nombre',
        'pais_id', 
        'departamento_id', 
        'municipio_id',
        'direccion',
        'telefono',
        'rtn',
        'fotos',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
    'fotos' => 'array',
    ];

    public function pais()
    {
        return $this->belongsTo(Paises::class);
    }
    /**
     * Define la relación inversa: una empresa pertenece a un departamento.
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
    /**
     * Define la relación inversa: una empresa pertenece a un municipio.
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }


    protected static function booted()
    {
        static::creating(function ($empresa) {
            if (auth()->check()) {
                $empresa->created_by = auth()->id();
            }
        });
        static::updating(function ($empresa) {
            if (auth()->check()) {
                $empresa->updated_by = auth()->id();
            }
        });
        static::deleting(function ($empresa) {
            if (auth()->check()) {
                $empresa->deleted_by = auth()->id();
                $empresa->save();
            }
        });
    }
}
