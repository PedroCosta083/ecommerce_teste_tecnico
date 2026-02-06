<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;

trait Authorizable
{
    protected function authorizeAction(string $ability, $model = null): void
    {
        if ($model) {
            $this->authorize($ability, $model);
        } else {
            $this->authorize($ability, get_class($this->getModelClass()));
        }
    }

    protected function checkPermission(string $permission): void
    {
        if (!auth()->user()->can($permission)) {
            throw new AuthorizationException('Você não tem permissão para realizar esta ação.');
        }
    }

    abstract protected function getModelClass(): string;
}
