<?php

namespace SG;

use SG\Attributes\Hook;
use SG\Contracts\Hookable;

class BlockAreas implements Hookable
{
    #[Hook('init')]
    public function register_cpt(): void
    {
        $labels = [
            'name'               => __('Block Areas', '7g'),
            'singular_name'      => __('Block Area', '7g'),
            'add_new'            => __('Add New', '7g'),
            'add_new_item'       => __('Add New Block Area', '7g'),
            'edit_item'          => __('Edit Block Area', '7g'),
            'new_item'           => __('New Block Area', '7g'),
            'view_item'          => __('View Block Area', '7g'),
            'search_items'       => __('Search Block Areas', '7g'),
            'not_found'          => __('No Block Areas found', '7g'),
            'not_found_in_trash' => __('No Block Areas found in Trash', '7g'),
            'parent_item_colon'  => __('Parent Block Area:', '7g'),
            'menu_name'          => __('Block Areas', '7g'),
        ];

        $args = [
            'labels'              => $labels,
            'hierarchical'        => false,
            'supports'            => ['title', 'editor', 'revisions'],
            'public'              => false,
            'publicly_queryable'  => is_admin(),
            'show_ui'             => true,
            'show_in_rest'        => true,
            'exclude_from_search' => true,
            'has_archive'         => false,
            'query_var'           => true,
            'can_export'          => true,
            'rewrite'             => false,
            'menu_icon'           => 'dashicons-layout',
            'show_in_menu'        => 'themes.php',
        ];

        register_post_type('block_area', $args);
    }

    public function get_block_area(\WP_Post|string $block_area): ?string
    {
        if (is_string($block_area)) {
            global $wpdb;
            $block_area_post_id = $wpdb->get_var(
                $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='block_area'", $block_area)
            );

            if ($block_area_post_id) {
                $block_area = get_post($block_area_post_id);
            }
        }

        if ($block_area instanceof \WP_Post) {
            return apply_filters('the_content', $block_area->post_content);
        }

        return null;
    }
}