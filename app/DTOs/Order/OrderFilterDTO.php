<?php

namespace App\DTOs\Order;

class OrderFilterDTO
{
    public function __construct(
        public ?int $userId = null,
        public ?string $status = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?float $minTotal = null,
        public ?float $maxTotal = null,
        public ?string $sortBy = 'created_at',
        public ?string $sortDirection = 'desc',
        public int $perPage = 15
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['user_id'] ?? null,
            $data['status'] ?? null,
            $data['date_from'] ?? null,
            $data['date_to'] ?? null,
            $data['min_total'] ?? null,
            $data['max_total'] ?? null,
            $data['sort_by'] ?? 'created_at',
            $data['sort_direction'] ?? 'desc',
            $data['per_page'] ?? 15
        );
    }
}