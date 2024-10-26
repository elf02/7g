<?php

namespace SG\Attributes;

use SG\Contracts\Hookable;
use ReflectionMethod;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class Hook
{
    public function __construct(protected string $tag, protected int|string $priority = 10)
    {
    }

    public function add_hook(Hookable $instance, ReflectionMethod $method): void
    {
        $priority = match (true) {
            $this->priority === 'first' => PHP_INT_MIN,
            $this->priority === 'last' => PHP_INT_MAX,
            is_int($this->priority) => $this->priority,
            default => 10,
        };

        add_filter(
            $this->tag,
            [$instance, $method->getName()],
            $priority,
            $method->getNumberOfParameters()
        );
    }
}
