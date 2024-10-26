<?php


require_once get_parent_theme_file_path('/vendor/autoload.php');

if (SG\Setup::acf_plugin_check()) {
    // Boot...
    add_action('after_setup_theme', '\SG\theme', PHP_INT_MIN);
}