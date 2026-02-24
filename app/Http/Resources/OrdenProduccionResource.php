<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdenProduccionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'producto' => new ProductoResource($this->producto),
            'cantidad' => $this->cantidad,
            'fecha_solicitud' => $this->fecha_solicitud,
            'fecha_entrega' => $this->fecha_entrega,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'empresa' => new EmpresaResource($this->empresa),
            'insumos' => OrdenProduccionInsumoResource::collection($this->insumos),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
