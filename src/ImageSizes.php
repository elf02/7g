<?php

namespace SG;

use SG\Attributes\Hook;
use SG\Contracts\Hookable;

class ImageSizes implements Hookable
{
    #[Hook('init')]
    public function register_image_sizes(): void
    {
        add_image_size('7g-lg', 2048, 1152, true);
    }

    #[Hook('image_size_names_choose')]
    public function image_sizes_names(array $sizes): array
    {
        $sizes['7g-lg'] = __('16:9 (Landscape)', '7g');

        return $sizes;
    }

    #[Hook('post_thumbnail_size', 5)]
    public function post_thumbnail_size(string $size): string
    {
        return $size === 'post-thumbnail' ? '7g-lg' : $size;
    }
}
