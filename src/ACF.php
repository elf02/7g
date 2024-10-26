<?php

namespace SG;

use Extended\ACF\Location;
use SG\Attributes\Hook;
use SG\Contracts\Hookable;

class ACF implements Hookable
{
    #[Hook('init')]
    public function register_blocks_and_fields(): void
    {
        foreach (glob(get_parent_theme_file_path('acf/blocks/*'), GLOB_ONLYDIR) as $block_path) {
            if ($block = register_block_type($block_path)) {
                register_extended_field_group([
                    'title' => $block->title,
                    'fields' => include $block_path . '/fields.php',
                    'location' => [
                        Location::where('block', $block->name),
                    ],
                ]);

                if (is_file($func = $block_path . '/functions.php')) {
                    include $func;
                }
            }
        }

        // Fields
        foreach (glob(get_parent_theme_file_path('acf/fields/*.php')) as $field_path) {
            $fields = include $field_path;
            register_extended_field_group($fields);
        }
    }

    #[Hook('allowed_block_types_all')]
    public function filter_allowed_block_types(bool|array $allowed_block_types, \WP_Block_Editor_Context $editor_context): bool|array
    {
        $acf_blocks = acf_get_block_types();

        if (
            !theme(BlockAreas::class) ||
            (
                isset($editor_context->post->post_type) &&
                $editor_context->post->post_type === 'block_area'
            )
        ) {
            unset($acf_blocks['acf/block-area']);
        }

        return array_keys($acf_blocks);
    }

    #[Hook('block_categories_all')]
    function block_categories_all(array $block_categories): array {
        $block_categories[] = [
          'slug' => '7g',
          'title' => '7g'
        ];

        return $block_categories;
    }

    #[Hook('after_setup_theme')]
    public function add_options_page(): void
    {
        acf_add_options_page([
            'page_title'        => __('Theme Settings', '7g'),
            'menu_title'        => __('Theme Settings', '7g'),
            'menu_slug'         => '7g-theme-settings',
            'update_button'     => __('Save', '7g'),
            'updated_message'   => __('Settings saved', '7g'),
            'capability'        => 'edit_posts',
            'redirect'          => false
        ]);
    }

    #[Hook('7g/block/attributes')]
    public function block_attributes(array $attributes, array $block, array $fields, string $path): array
    {
        $global_attributes = [
            'name' => basename($path),
            'id' => !empty($block['anchor']) ? $block['anchor'] : ''
        ];

        return array_merge($global_attributes, $attributes);
    }

    #[Hook('after_setup_theme')]
    public function acf_settings(): void
    {
        // Removed ACF frontend wrapper
        add_filter('acf/blocks/wrap_frontend_innerblocks', '__return_false', 10, 2);

        // Hide ACF menu
        add_filter('acf/settings/show_admin', '__return_false');
    }

    public function get_block_template(string $block_name, array $args = []): void
    {
        get_template_part("acf/blocks/{$block_name}/template", args: $args);
    }
}
