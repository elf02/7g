<?php get_header(); ?>

<?php
    if (have_posts()) {
        foreach (SG\loop() as $post) {
            get_template_part('template-parts/content', get_post_type());
        }
    }
?>

<?php get_footer(); ?>