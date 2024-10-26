<?php

$wrapper_attrs = \SG\wrapper_attributes(
    apply_filters('7g/block/attributes', [
        'class' => 'alignwide',
        'load:on' => 'idle',
        'load:on:media' => '(max-width: 768px)'
    ], [], [], __DIR__)
);

?>

<block-component <?= $wrapper_attrs ?>>
    <nav class="navigation-mobile" aria-label="Main">
        <div class="logo">
            <?php get_template_part('template-parts/svg/logo') ?>
        </div>
        <button class="button" data-ref="mobileToggle" aria-expanded="false" aria-controls="nav-main-mobile">
            <?php get_template_part('template-parts/svg/burger') ?>
            <?php get_template_part('template-parts/svg/close') ?>
        </button>
        <?php if (has_nav_menu('main-nav')): ?>
            <?php
                wp_nav_menu([
                    'theme_location' => 'main-nav',
                    'menu_id' => 'navigation-main-mobile',
                    'container_class' => 'navigation-main-mobile',
                    'container_id' => 'NavigationMobile',
                    'depth' => 1
                ]);
            ?>
        <?php endif; ?>
    </nav>
</block-component>
