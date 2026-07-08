<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KitchenOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'special_instructions' => $this->special_instructions,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'total_amount' => $this->total_amount,
            'created_at' => $this->created_at?->toISOString(),
            'table' => [
                'id' => $this->table?->id,
                'table_number' => $this->table?->table_number,
                'section' => $this->table?->section,
            ],
            'items' => KitchenOrderItemResource::collection($this->whenLoaded('orderItems')),
        ];
    }
}
