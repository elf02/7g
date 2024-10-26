<?php

namespace SG;

use SG\Attributes\Hook;
use SG\Contracts\Hookable;

class Setup implements Hookable
{
    public static function acf_plugin_check(): bool
    {
        if (class_exists('acf')) return true;

        $notice = esc_html__('Please install or activate the required plugin: Advanced Custom Fields (ACF) Pro.', '7g');

        add_filter('template_redirect', function() use ($notice) {
            wp_die($notice);
        });

        add_action('admin_notices', function () use ($notice) {
            echo '<div class="notice notice-error"><p>' . $notice . '</p></div>';
        });

        return false;
    }

    #[Hook('after_setup_theme')]
    public function register_menus(): void
    {
        register_nav_menus([
            'main-nav' => __('Main Navigation', '7g')
        ]);
    }

    #[Hook('after_setup_theme')]
    public function theme_support(): void
    {
        add_theme_support(
			'html5',
			[
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
            ]
		);

        add_theme_support('post-thumbnails');
        add_theme_support('editor-styles');

        remove_theme_support('block-templates');
    }

    #[Hook('after_setup_theme')]
    public function load_textdomain(): void
    {
        load_theme_textdomain('7g', get_parent_theme_file_path('languages'));
    }

    #[Hook('phpmailer_init')]
    public function smtp_mailpit($phpmailer): void
    {
        if (local_env()) {
            $phpmailer->IsSMTP();
            $phpmailer->Host = '127.0.0.1';
            $phpmailer->Port = 1025;
            $phpmailer->Username = '';
            $phpmailer->Password = '';
            $phpmailer->SMTPAuth = true;
        }
    }
}
