<?php

namespace SG;

use SG\Traits\HasStore;

class ImageHelper
{
    use HasStore;

    protected $path;
    protected $url;

    public function __construct(protected int|string $id)
    {
        $this->path = get_attached_file($this->id);
        $this->url = wp_get_attachment_url($this->id);
    }

    public function path(): string
    {
        return $this->path;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function width(): int
    {
        return $this->meta('width');
    }

    public function height(): int
    {
        return $this->meta('height');
    }

    public function aspect(): float|null
    {
        $w = intval($this->width());
        $h = intval($this->height());

        if ($w && $h > 0) {
            return $w / $h;
        }

        return null;
    }

    public function alt(): string
    {
        if (!$this->store_has('alt')) {
            $this->store_set('alt', get_post_meta($this->id, '_wp_attachment_image_alt', true));
        }

        return $this->store_get('alt');
    }

    public function caption(): string
    {
        if (!$this->store_has('caption')) {
            $this->store_set('caption', get_post_field('post_excerpt', $this->id));
        }

        return $this->store_get('caption');
    }

    public function description(): string
    {
        if (!$this->store_has('description')) {
            $this->store_set('description', get_post_field('post_content', $this->id));
        }

        return $this->store_get('description');
    }

    public function meta(string $key): mixed
    {
        if (!$this->store_has('meta')) {
            $this->store_set('meta', wp_get_attachment_metadata($this->id));
        }

        $meta = $this->store_get('meta');
        if (is_array($meta) && array_key_exists($key, $meta)) {
            return $meta[$key];
        }

        return null;
    }

    public function resize(int $w, int $h, string $crop = 'default'): string
    {
        return theme(DynamicResize::class)->resize_image_url($this->url, $w, $h, $crop);
    }

    public function placeholder(): string
    {
        return 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
    }

    public function resp_image_tag(string $size, string $max_width, bool $lazy = true): string
    {
        $attributes = [
            'src' => wp_get_attachment_image_url($this->id, $size),
            'srcset' => wp_get_attachment_image_srcset($this->id, $size),
            'sizes' => "(max-width: {$max_width}) 100vw, {$max_width}"
        ];

        if ($lazy) {
            $attributes['loading'] = 'lazy';
        }

        return wp_get_attachment_image($this->id, $size, false, $attributes);
    }
}
