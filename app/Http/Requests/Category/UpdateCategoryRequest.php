<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->parent_id === 'none') {
            $this->merge([
                'parent_id' => null,
            ]);
        }
    }

    public function rules(): array
    {
        $categoryId = $this->route('category');

        return [
            'name' => 'sometimes|string|max:255',
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($categoryId)
            ],
            'description' => 'nullable|string',
            'parent_id' => 'sometimes|nullable|exists:categories,id',
            'active' => 'boolean',
        ];
    }
}
