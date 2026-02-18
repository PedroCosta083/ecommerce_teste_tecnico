<?php

namespace App\Rules;

use Closure;
use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidParentCategory implements ValidationRule
{
    private ?int $categoryId;

    public function __construct(?int $categoryId = null)
    {
        $this->categoryId = $categoryId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {
            return;
        }

        $parentCategory = Category::find($value);

        if (!$parentCategory) {
            $fail('A categoria pai não existe.');
            return;
        }

        // Evitar auto-referência
        if ($this->categoryId && $value == $this->categoryId) {
            $fail('Uma categoria não pode ser pai de si mesma.');
            return;
        }

        // Evitar referência circular
        if ($this->categoryId && $this->wouldCreateCircularReference($value, $this->categoryId)) {
            $fail('Esta seleção criaria uma referência circular.');
        }
    }

    private function wouldCreateCircularReference(int $parentId, int $childId): bool
    {
        $current = Category::find($parentId);

        while ($current && $current->parent_id) {
            if ($current->parent_id == $childId) {
                return true;
            }
            $current = $current->parent;
        }

        return false;
    }
}
