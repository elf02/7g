<?php

use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Message;
use Extended\ACF\Fields\PostObject;

return [
    Message::make($block->title . ' Block', 'block_title'),

    Tab::make(__('Content', '7g'))
        ->selected(),

    PostObject::make(__('Block Area', '7g'), 'block_area')
        ->postTypes(['block_area'])
        ->postStatus(['publish'])
        ->nullable()
        ->format('object')
];