<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'price'       => $this->price,
            'cost_price'  => $this->cost_price,
            'quantity'    => $this->quantity,
            'min_quantity'=> $this->min_quantity,
            'active'      => $this->active,
            'category'    => new CategoryResource($this->whenLoaded('category')),
            'tags'        => TagResource::collection($this->whenLoaded('tags')),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}