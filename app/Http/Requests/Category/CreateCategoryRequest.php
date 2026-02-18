<?php

namespace App\Http\Requests\Category;

use App\Rules\UniqueSlug;
use App\Rules\ValidParentCategory;
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
            'slug' => ['required', 'string', 'max:255', new UniqueSlug('categories')],
            'description' => 'nullable|string',
            'parent_id' => ['nullable', new ValidParentCategory()],
            'active' => 'boolean',
        ];
    }
}
