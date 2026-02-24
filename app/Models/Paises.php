<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paises extends Model
{
    /** @use HasFactory<\Database\Factories\PaiseFactory> */
    use HasFactory, SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paises'; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre_pais',
        'created_by', // Importante para asignar en la factory
        'updated_by', // Importante para asignar en la factory
        'deleted_by' // No suele ir en fillable a menos que quieras asignarlo m
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'created_at' => 'datetime',
        // 'updated_at' => 'datetime',
        // 'deleted_at' => 'datetime',
    ];

    public function departamentos()
    {
        return $this->hasMany(Departamento::class);
    }

    public function personas()
    {
        return $this->hasOne(Persona::class);
    }
}
