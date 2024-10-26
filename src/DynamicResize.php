<?php

namespace SG;

use SG\Attributes\Hook;
use SG\Contracts\Hookable;

class DynamicResize implements Hookable
{
    protected static $url_part = 'resized';
    protected static $rewrite_tag = 'resized-images';
    protected static $table_name = 'resized_images';
    protected $resize_params = [];

    protected function table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . self::$table_name;
    }

    #[Hook('after_setup_theme')]
    public function create_db_table(): void
    {
        global $wpdb;

        $table_name = $this->table_name();
        $charset = $wpdb->get_charset_collate();

        $table_exists = $wpdb->get_var(
            $wpdb->prepare("SHOW TABLES LIKE %s", $table_name)
        ) === $table_name;

        if (!$table_exists) {
            $sql = "CREATE TABLE {$table_name} (
                resize_params varchar(50) NOT NULL
            ) {$charset};";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            $wpdb->query("ALTER TABLE {$table_name} ADD PRIMARY KEY(`resize_params`);");
        }
    }

    #[Hook('init')]
    public function add_rewrite_tag(): void
    {
        add_rewrite_tag('%' . self::$rewrite_tag . '%', '([^&]+)');
    }

    #[Hook('generate_rewrite_rules')]
    public function add_rewrite_rule(\WP_Rewrite $wp_rewrite): void
    {
        $rewrite_tag = self::$rewrite_tag;
        $rewrite_url = ltrim(trailingslashit(
            wp_make_link_relative(wp_upload_dir()['baseurl'])
        ), '/') . self::$url_part;

        $wp_rewrite->rules = array_merge(
            ["^{$rewrite_url}/?(.*?)/?$" => "index.php?{$rewrite_tag}=\$matches[1]"],
            $wp_rewrite->rules
        );
    }

    #[Hook('parse_request')]
    public function parse_request(\WP $wp): void
    {
        if (isset($wp->query_vars[self::$rewrite_tag])) {
            $this->resize_image_from_request($wp->query_vars[self::$rewrite_tag]);
        }
    }

    #[Hook('shutdown', 'first')]
    public function shutdown(): void
    {
        if (!empty($this->resize_params)) {
            global $wpdb;

            $table_name = $this->table_name();
            $placeholders = implode(', ', array_fill(0, count($this->resize_params), '(%s)'));

            $wpdb->query(
                $wpdb->prepare(
                    "INSERT IGNORE INTO {$table_name} (resize_params) VALUES {$placeholders}",
                    $this->resize_params
                )
            );
        }

        flush_rewrite_rules(false);
    }

    protected function resize_image_from_request(string $relative_path): void
    {
        $matched = preg_match(
            '/(.+)-(\d+)x(\d+)-c-(.+)(\..*)$/',
            $relative_path,
            $matches
        );

        if ($matched) {
            $upload_dir = wp_upload_dir();
            $original_image_path = $upload_dir['basedir'] . '/' . $matches[1] . $matches[5];
            $w = (int) $matches[2];
            $h = (int) $matches[3];
            $crop = $matches[4];

            $wp_crop = match (true) {
                $crop === 'default' => false,
                $crop === 'center' => true,
                strpos($crop, '-') !== false => explode('-', $crop),
                default => false
            };

            if (is_file($original_image_path)) {
                global $wpdb;

                $table_name = $this->table_name();
                $resize_params = $wpdb->get_row(
                    $wpdb->prepare("SELECT * FROM {$table_name} WHERE resize_params = %s", [
                        "{$w}x{$h}-c-{$crop}"
                    ])
                );

                if (!empty($resize_params)) {
                    $new_image_path = $upload_dir['basedir'] . '/' . self::$url_part .'/' . $relative_path;
                    $new_image_url = $upload_dir['baseurl'] . '/' . self::$url_part . '/' . $relative_path;

                    $new_img = wp_get_image_editor($original_image_path);
                    $new_img->resize($w, $h, $wp_crop);
                    $new_img->save($new_image_path);

                    wp_redirect($new_image_url);
                    exit();
                }
            }
        }

        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        if ($tmpl = get_404_template()) {
            include $tmpl;
        }
        exit();
    }

    public function resize_image_url(string|int $url_or_id, int $w, int $h, string $crop = 'default'): string
    {
        $upload_dir = wp_upload_dir();
        $url = is_int($url_or_id) ? wp_get_attachment_url($url_or_id) : $url_or_id;
        $resize_param = "{$w}x{$h}-c-{$crop}";

        $relative_url = str_replace($upload_dir['baseurl'], '', $url);
        $ext = pathinfo($url)['extension'];
        $new_relative_url = str_replace(".{$ext}", "-{$resize_param}.{$ext}", $relative_url);
        $new_url = trailingslashit($upload_dir['baseurl']) . self::$url_part . $new_relative_url;

        $this->resize_params[] = $resize_param;

        return $new_url;
    }
}
