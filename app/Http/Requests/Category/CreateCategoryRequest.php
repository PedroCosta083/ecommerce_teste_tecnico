<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'active' => 'boolean',
        ];
    }
}
