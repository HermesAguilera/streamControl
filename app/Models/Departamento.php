<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    /** @use HasFactory<\Database\Factories\DepartamentoFactory> */
    use HasFactory, SoftDeletes;
    protected $table = 'departamentos'; 

    protected $fillable = [
        'nombre_departamento',
        'pais_id',
        'created_by',
        'updated_by',
        //'deleted_by'
    ];

    
    public function pais()
    {
        return $this->belongsTo(Paises::class);
    }
}
