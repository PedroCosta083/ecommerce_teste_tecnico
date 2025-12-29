<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'cost_price'  => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:0',
            'min_quantity'=> 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'active'      => 'boolean',
            'tag_ids'     => 'array',
            'tag_ids.*'   => 'exists:tags,id',
        ];
    }
}