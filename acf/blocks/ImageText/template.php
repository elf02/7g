<?php

$fields = \SG\fields();

$wrapper_attrs = $is_preview ?
    '' :
    get_block_wrapper_attributes(
        apply_filters('7g/block/attributes', [
            'load:on' => $fields->parallax ? 'visible' : 'prevent',
            'load:on:media' => '(min-width: 768px)'
        ], $block, $fields->all(), __DIR__)
    );

$image = \SG\image($fields->image);

?>

<block-component <?= $wrapper_attrs ?>>
    <div class="image-text-wrap <?= $fields->image_position ?>">
        <figure <?php if ($fields->parallax): ?>data-ref="rellax"<?php endif; ?>>
            <img
                class="lazyload"
                src="<?= $image->resize(768, 0) ?>"
                width="<?= $image->width() ?>"
                height="<?= $image->height() ?>"
                srcset="<?= $image->placeholder() ?>"
                data-srcset="
                    <?= $image->resize(1920, 0) ?> 1920w,
                    <?= $image->resize(1600, 0) ?> 1600w,
                    <?= $image->resize(1440, 0) ?> 1440w,
                    <?= $image->resize(1366, 0) ?> 1366w,
                    <?= $image->resize(1024, 0) ?> 1024w,
                    <?= $image->resize(768, 0) ?> 768w,
                    <?= $image->resize(640, 0) ?> 640w
                "
                data-sizes="auto"
                alt="<?= $image->alt() ?>"
            >
            <?php if ($image->caption()): ?>
                <figcaption><?= $image->caption() ?></figcaption>
            <?php endif; ?>
        </figure>
        <div class="text">
            <?php get_template_part('template-parts/headline', args: $fields->all()) ?>
            <?= $fields->text ?>
        </div>
    </div>
</block-component>
