<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueSlug implements ValidationRule
{
    private string $table;
    private ?int $ignoreId;

    public function __construct(string $table, ?int $ignoreId = null)
    {
        $this->table = $table;
        $this->ignoreId = $ignoreId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table($this->table)
            ->where('slug', $value);

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        // Considerar soft deletes se a tabela for products
        if ($this->table === 'products') {
            $query->whereNull('deleted_at');
        }

        if ($query->exists()) {
            $fail('Este slug já está em uso.');
        }
    }
}
