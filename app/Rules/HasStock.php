<?php

namespace App\Rules;

use Closure;
use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;

class HasStock implements ValidationRule
{
    private int $quantity;

    public function __construct(int $quantity)
    {
        $this->quantity = $quantity;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $product = Product::find($value);

        if (!$product) {
            $fail('O produto não existe.');
            return;
        }

        if ($product->quantity < $this->quantity) {
            $fail("Estoque insuficiente. Disponível: {$product->quantity}, solicitado: {$this->quantity}.");
        }
    }
}
