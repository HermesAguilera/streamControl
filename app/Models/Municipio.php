<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Municipio extends Model
{
    /** @use HasFactory<\Database\Factories\MunicipioFactory> */
    use HasFactory, SoftDeletes;
    protected $table = 'municipios';

    protected $fillable = [
        'nombre_municipio',
        'departamento_id',
        'pais_id',
        'created_by',
        'updated_by',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function empresas()
    {
        return $this->hasMany(Empresa::class);
    }

    public function personas()
    {
        return $this->hasOne(Persona::class);
    }
}
