<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:pending,processing,shipped,delivered,cancelled',
            'shipping_address' => 'sometimes|array',
            'shipping_address.street' => 'required_with:shipping_address|string|max:255',
            'shipping_address.city' => 'required_with:shipping_address|string|max:255',
            'shipping_address.state' => 'required_with:shipping_address|string|max:255',
            'shipping_address.zip' => 'required_with:shipping_address|string|max:20',
            'billing_address' => 'sometimes|array',
            'billing_address.street' => 'required_with:billing_address|string|max:255',
            'billing_address.city' => 'required_with:billing_address|string|max:255',
            'billing_address.state' => 'required_with:billing_address|string|max:255',
            'billing_address.zip' => 'required_with:billing_address|string|max:20',
            'notes' => 'nullable|string',
        ];
    }
}