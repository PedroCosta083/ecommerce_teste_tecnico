<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    protected $repository;

    protected function findOrFail(int $id): ?Model
    {
        return $this->repository->findById($id);
    }

    protected function filterNullValues(array $data): array
    {
        return array_filter($data, fn($value) => $value !== null);
    }

    protected function updateModel(Model $model, array $data): Model
    {
        $this->repository->update($model, $this->filterNullValues($data));
        return $model->fresh();
    }

    protected function deleteModel(int $id): bool
    {
        $model = $this->findOrFail($id);
        return $model ? $this->repository->delete($model) : false;
    }
}
