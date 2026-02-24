<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    /** @use HasFactory<\Database\Factories\PersonaFactory> */
    use HasFactory, SoftDeletes;


    /**
     * Define la relación: una persona pertenece a un municipio.
     */

    /**
     * Define la relación: una persona pertenece a un país.
     */

    /**
     * Una persona pertenece a una empresa (relación 1:1).
     */

    /**
     * Define la relación: una persona pertenece a un departamento.
     */

    protected $table = 'personas';

    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'dni',
        'tipo_persona',
        'direccion',
        'municipio_id',
        'departamento_id', // debe ser nullable en la migración y en el modelo
        'telefono',
        'sexo',
        'fecha_nacimiento',
        'pais_id',
        'fotografia', // Campo para la ruta de la fotografía
        'empresa_id', // Nueva relación
        'created_by',
        'updated_by',
    ];

    /**
     * Define la relación: una persona pertenece a un municipio.
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }


    /**
     * Define la relación: una persona pertenece a un país.
     */
    public function pais()
    {
        return $this->belongsTo(Paises::class);
    }
    

    /**
     * Una persona pertenece a una empresa (relación 1:1).
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    /**
     * Define la relación: una persona pertenece a un departamento.
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function perfiles()
    {
        return $this->hasMany(Perfil::class);
    }
}

