<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <title><?php wp_title(); ?></title>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <a href="#main-content" class="visually-hidden">Skip to main content</a>
    <header class="container">
        <?php \SG\get_block_template('NavigationMain'); ?>
        <?php \SG\get_block_template('NavigationMobile'); ?>
    </header>
    <main class="container" id="main-content">