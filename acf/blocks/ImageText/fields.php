<?php

use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Message;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Message::make($block->title . ' Block', 'block_title'),

    Tab::make(__('Content', '7g'))
        ->selected(),

    Image::make(__('Image', '7g'), 'image')
        ->acceptedFileTypes(['jpg', 'jpeg', 'png', 'webp'])
        ->format('id')
        ->previewSize('medium')
        ->required(),

    Select::make(__('Image Position', '7g'), 'image_position')
        ->choices(['image-left' => __('Left', '7g'), 'image-right' => __('Right', '7g')])
        ->default('image-left')
        ->format('value')
        ->column(20)
        ->required(),

    TrueFalse::make(__('Parallax', '7g'), 'parallax')
        ->default(false)
        ->column(80)
        ->stylized(),

    WYSIWYGEditor::make(__('Text', '7g'), 'text')
        ->tabs('all')
        ->disableMediaUpload()
        ->lazyLoad()
        ->required(),

    Tab::make(__('Settings', '7g')),

    ...include(get_parent_theme_file_path('/acf/fields/clone/BlockSettings.php')),
];