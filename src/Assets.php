<?php

namespace SG;

use SG\Attributes\Hook;
use SG\Contracts\Hookable;

class Assets implements Hookable
{
    protected $asset_manifest = null;

    #[Hook('after_setup_theme')]
    public function add_editor_styles(): void
    {
        $relative_path = str_replace(
            get_parent_theme_file_path(),
            '',
            $this->asset_path('assets/editor.scss')
        );

        add_editor_style($relative_path);
    }

    #[Hook('script_loader_tag')]
    public function filter_script_loader_tag(string $tag, string $handle, string $src): string
    {
        if (wp_scripts()->get_data($handle, 'module')) {
            return str_replace('></script>', ' type="module"></script>', $tag);
        }

        return $tag;
    }

    #[Hook('wp_enqueue_scripts')]
    public function enqueue_assets(): void
    {
        wp_enqueue_script(
            '7g',
            $this->asset_url('assets/main.js'),
            [],
            null
        );
        wp_script_add_data('7g', 'module', true);

        wp_enqueue_style(
            '7g',
            $this->asset_url('assets/main.scss'),
            [],
            null
        );
    }

    protected function asset_from_manifest(string $asset): array
    {
        if (is_null($this->asset_manifest)) {
            if (is_file($manifest_file = get_parent_theme_file_path('dist/.vite/manifest.json'))) {
                $this->asset_manifest = json_decode(file_get_contents($manifest_file), true);
            }
        }

        $asset = trim($asset, '/');

        return [
            $asset_file = $this->asset_manifest[$asset]['file'] ?? $asset,
            get_parent_theme_file_path('dist/' . $asset_file)
        ];
    }

    public function asset_url(string $asset): string
    {
        [$asset_file, $asset_path] = $this->asset_from_manifest($asset);

        // check for hot module replacement
        if (is_file($vite_hot_file = get_parent_theme_file_path('dist/hot'))) {
            return trailingslashit(trim(file_get_contents($vite_hot_file))) . $asset;
        }

        return is_file($asset_path) ?
            get_parent_theme_file_uri('dist/' . $asset_file) :
            get_parent_theme_file_uri($asset_file);
    }

    public function asset_path(string $asset): string
    {
        [$asset_file, $asset_path] = $this->asset_from_manifest($asset);

        return is_file($asset_path) ?
            $asset_path :
            get_parent_theme_file_path($asset_file);
    }
}
