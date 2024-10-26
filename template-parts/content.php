<article <?php post_class(); ?>>
  <header>
    <h2>
      <a href="<?php the_permalink(); ?>">
        <?php the_title(); ?>
      </a>
    </h2>
  </header>
  <div>
    <?php the_content(); ?>
  </div>
</article>
