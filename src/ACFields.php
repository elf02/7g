<?php

namespace SG;

use SG\Traits\HasStore;

class ACFields
{
    use HasStore;

    public static function createFromFields(mixed $post_id = false, bool $format_value = true): ACFields
    {
        return new self(get_fields($post_id, $format_value) ?: []);
    }

    public function __construct(array $fields)
    {
        $this->store_hydrate($fields);
    }

    public function all(): array
    {
        return $this->store_get();
    }

    public function get(string $key): mixed
    {
        return $this->store_get($key);
    }

    public function getAsObj(string $key): ACFields
    {
        $item = $this->get($key);

        return new self(is_array($item) ? $item : []);
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->store_set($key, $value);
    }

    public function __isset($key)
    {
        return $this->store_has($key);
    }
}
