<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'session_id' => $this->session_id,
            'items' => CartItemResource::collection($this->whenLoaded('cartItems')),
            'total_items' => $this->cartItems->sum('quantity'),
            'total_price' => $this->cartItems->sum(fn($item) => $item->quantity * $item->product->price),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}