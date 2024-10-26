<?php

$wrapper_attrs = \SG\wrapper_attributes(
    apply_filters('7g/block/attributes', [
        'class' => 'alignwide'
    ], [], [], __DIR__)
);

?>

<block-component <?= $wrapper_attrs ?>>
    <nav class="navigation" aria-label="Main">
        <div class="logo">
            <?php get_template_part('template-parts/svg/logo') ?>
        </div>
        <?php if (has_nav_menu('main-nav')): ?>
            <?php
                wp_nav_menu([
                    'theme_location' => 'main-nav',
                    'menu_id' => 'navigation-main',
                    'container_class' => 'navigation-main',
                    'depth' => 1
                ]);
            ?>
        <?php endif; ?>
    </nav>
</block-component>
