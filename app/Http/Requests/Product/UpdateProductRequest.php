<?php

namespace App\Http\Requests\Product;

use App\Rules\UniqueSlug;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $productId = is_object($product) ? $product->id : $product;
        
        return [
            'name' => 'sometimes|string|max:255',
            'slug' => ['sometimes', 'string', 'max:255', new UniqueSlug('products', $productId)],
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
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