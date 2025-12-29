<?php

namespace App\Http\Requests\StockMovement;

use Illuminate\Foundation\Http\FormRequest;

class CreateStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entrada,saida,ajuste,venda,devolucao',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
        ];
    }
}