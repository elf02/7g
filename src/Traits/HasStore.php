<?php

namespace SG\Traits;

trait HasStore
{
    protected $store = [];

    protected function store_hydrate(array $items): void
    {
        $this->store = $items;
    }

    protected function store_has(string $key): bool
    {
        return array_key_exists($key, $this->store);
    }

    protected function store_get(?string $key = null): mixed
    {
        if (is_null($key)) {
            return $this->store;
        }

        return $this->store[$key] ?? null;
    }

    protected function store_set(string $key, mixed $value): void
    {
        $this->store[$key] = $value;
    }
}
