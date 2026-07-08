<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KitchenOrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'subtotal' => $this->subtotal,
            'notes' => $this->notes,
            'status' => $this->status,
            'menu' => [
                'id' => $this->menu?->id,
                'name' => $this->menu?->name,
                'price' => $this->menu?->price,
            ],
        ];
    }
}
