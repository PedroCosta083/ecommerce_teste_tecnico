<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');
        
        return [
            'name' => 'sometimes|string|max:255',
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId)
            ],
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'cost_price'  => 'sometimes|numeric|min:0',
            'quantity'    => 'sometimes|integer|min:0',
            'min_quantity'=> 'sometimes|integer|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'active'      => 'boolean',
            'tag_ids'     => 'nullable|array',
            'tag_ids.*'   => 'nullable|exists:tags,id',
        ];
    }
}