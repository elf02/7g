<?php

use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Number;
use Extended\ACF\Fields\Gallery;
use Extended\ACF\Fields\Message;
use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\TrueFalse;

return [
    Message::make($block->title . ' Block', 'block_title'),

    Tab::make(__('Content', '7g'))
        ->selected(),

    Gallery::make(__('Images', '7g'), 'images')
        ->acceptedFileTypes(['jpg', 'jpeg', 'png', 'webp'])
        ->minFiles(1)
        ->format('id')
        ->previewSize('medium')
        ->prependFiles()
        ->required(),

    TrueFalse::make(__('Loop', '7g'), 'loop')
        ->default(false)
        ->column(30)
        ->stylized(),

    TrueFalse::make(__('Pagination', '7g'), 'pagination')
        ->default(false)
        ->column(30)
        ->stylized(),

    TrueFalse::make(__('Navigation', '7g'), 'navigation')
        ->default(false)
        ->column(30)
        ->stylized(),

    TrueFalse::make(__('Autoplay', '7g'), 'autoplay')
        ->default(false)
        ->column(30)
        ->stylized(),

    Number::make(__('Autoplay Delay', '7g'), 'autoplay_delay')
        ->conditionalLogic([
            ConditionalLogic::where('autoplay', '==', '1')
        ])
        ->helperText(__('Autoplay delay in milliseconds.', '7g'))
        ->min(2000)
        ->default(3000)
        ->column(70)
        ->required(),



    Tab::make(__('Settings', '7g')),

    ...include(get_parent_theme_file_path('/acf/fields/clone/BlockSettings.php')),
];