<?php

namespace App\Http\Requests\Cart;

use App\Rules\HasStock;
use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id', new HasStock($this->input('quantity', 1))],
            'quantity' => 'required|integer|min:1',
            'user_id' => 'nullable|exists:users,id',
            'session_id' => 'nullable|string',
        ];
    }
}