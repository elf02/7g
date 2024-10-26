<?php

$fields = \SG\fields();

$swiper_settings = [
	'roundLengths' => true
];

if ($fields->loop) {
    $swiper_settings['loop'] = true;
}

if ($fields->autoplay) {
    $swiper_settings['autoplay'] = [
		'delay' => $fields->autoplay_delay ?? 3000,
		'disableOnInteraction' => true
	];
}

if ($fields->pagination) {
    $swiper_settings['pagination'] =[
		'el' => '.swiper-pagination',
		'clickable' => true
	];
}

if ($fields->navigation) {
    $swiper_settings['navigation'] =[
		'nextEl' => '.swiper-button-next',
        'prevEl' => '.swiper-button-prev',
	];
}

$wrapper_attrs = $is_preview ?
    '' :
    get_block_wrapper_attributes(
        apply_filters('7g/block/attributes', [
            'load:on' => 'visible',
            'data-swiper-settings' => json_encode($swiper_settings)
        ], $block, $fields->all(), __DIR__)
    );

?>

<block-component <?= $wrapper_attrs ?>>
    <?php get_template_part('template-parts/headline', args: $fields->all()) ?>

    <?php if (!empty($fields->images)): ?>
        <div class="swiper" data-ref="swiper">
            <div class="swiper-wrapper">
            <?php
                foreach ($fields->images as $image_id):
                    $image = \SG\image($image_id);
            ?>
                    <div class="swiper-slide">
                        <figure>
                            <img
                                class="lazyload"
                                src="<?= $image->resize(0, 540) ?>"
                                width="<?= round(540 * $image->aspect()) ?>"
                                height="540"
                                srcset="<?= $image->placeholder() ?>"
                                data-srcset="
                                    <?= $image->resize(0, 1080) ?> <?= round(1080 * $image->aspect()) ?>w,
                                    <?= $image->resize(0, 860) ?> <?= round(860 * $image->aspect()) ?>w,
                                    <?= $image->resize(0, 540) ?> <?= round(540 * $image->aspect()) ?>w,
                                    <?= $image->resize(0, 385) ?> <?= round(385 * $image->aspect()) ?>w,
                                    <?= $image->resize(0, 250) ?> <?= round(250 * $image->aspect()) ?>w
                                "
                                data-sizes="auto"
                                alt="<?= $image->alt() ?>"
                            >
                            <?php if ($image->caption()): ?>
                                <figcaption><?= $image->caption() ?></figcaption>
                            <?php endif; ?>
                        </figure>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (isset($swiper_settings['pagination'])): ?>
                <div class="swiper-pagination"></div>
            <?php endif; ?>
            <?php if (isset($swiper_settings['navigation'])): ?>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</block-component>
