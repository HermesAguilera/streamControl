<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdenProduccionInsumoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'insumo' => new ProductoResource($this->insumo),
            'cantidad_utilizada' => $this->cantidad_utilizada,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
