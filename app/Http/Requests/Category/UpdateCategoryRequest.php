<?php

namespace App\Http\Requests\Category;

use App\Rules\UniqueSlug;
use App\Rules\ValidParentCategory;
use Illuminate\Foundation\Http\FormRequest;

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
        $category = $this->route('category');
        $categoryId = is_object($category) ? $category->id : $category;

        return [
            'name' => 'sometimes|string|max:255',
            'slug' => ['sometimes', 'string', 'max:255', new UniqueSlug('categories', $categoryId)],
            'description' => 'nullable|string',
            'parent_id' => ['sometimes', 'nullable', new ValidParentCategory($categoryId)],
            'active' => 'boolean',
        ];
    }
}
