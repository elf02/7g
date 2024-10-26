<?php

namespace SG;

use SG\Contracts\Hookable;
use SG\Attributes\Hook;

function theme(string $component = ''): mixed
{
    static $bindings = [];

    if (empty($bindings)) {
        $bindings = apply_filters('7g/components', [
            Setup::class         => new Setup(),
            Assets::class        => new Assets(),
            CPT::class           => new CPT(),
            ACF::class           => new ACF(),
            BlockAreas::class    => new BlockAreas(),
            DynamicResize::class => new DynamicResize(),
            //ImageSizes::class  => new ImageSizes(),
        ]);

        // hook attributes
        foreach ($bindings as $binding) {
            if (!$binding instanceof Hookable) {
                continue;
            }

            $reflect = new \ReflectionClass($binding::class);

            foreach ($reflect->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $attributes = $method->getAttributes(
                    Hook::class,
                    \ReflectionAttribute::IS_INSTANCEOF
                );

                if (empty($attributes)) {
                    continue;
                }

                foreach ($attributes as $attribute) {
                    $hook = $attribute->newInstance();
                    $hook->add_hook($binding, $method);
                }
            }
        }
    }

    if ($component === '') {
        return $bindings;
    }
    else if (isset($bindings[$component])) {
        return $bindings[$component];
    }

    return null;
}

function asset_url(string $asset): string
{
    return theme(Assets::class)->asset_url($asset);
}

function asset_path(string $asset): string
{
    return theme(Assets::class)->asset_path($asset);
}

function wrapper_attributes(array $attributes)
{
    $attributes = array_filter($attributes);

    return implode(' ', array_map(function($v, $k) {
        $v = esc_attr($v);
        return "{$k}=\"{$v}\"";
    }, $attributes, array_keys($attributes)));
}

function get_block_template(string $block_name, array $args = []): void
{
    theme(ACF::class)->get_block_template($block_name, $args);
}

function image(int|string $id): ImageHelper
{
    return new ImageHelper($id);
}

function get_block_area(\WP_Post|string $block_area): string
{
    return theme(BlockAreas::class)->get_block_area($block_area);
}

function fields(mixed $post_id = false, bool $format_value = true, array $items = []): ACFields
{
    if (!empty($items)) {
        return new ACFields($items);
    } else {
        return ACFields::createFromFields($post_id, $format_value);
    }
}

function local_env(): bool
{
    return in_array(wp_get_environment_type(), ['local', 'development']);
}

function str_swap(string $str, array $swap): string
{
    return str_replace(array_keys($swap), array_values($swap), $str);
}

function loop(?iterable $iterable = null): \Generator
{
    if ($iterable === null) {
        $iterable = $GLOBALS['wp_query'];
    }

    $posts = $iterable;

    if (is_object($iterable) && property_exists($iterable, 'posts')) {
        $posts = $iterable->posts;
    }

    if (!is_array($posts)) {
        throw new \InvalidArgumentException(sprintf('Expected an array, received %s instead.', gettype($posts)));
    }

    global $post;

    $save_post = $post;

    try {
        foreach ($posts as $post) {
            setup_postdata($post);
            yield $post;
        }
    } finally {
        wp_reset_postdata();
        $post = $save_post;
    }
}
