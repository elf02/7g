<?php

use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;

return [
    'title' => __('Theme Settings', '7g'),
    'fields' => [
        WYSIWYGEditor::make(__('Text', '7g'), 'text')
            ->tabs('visual')
            ->disableMediaUpload()
            ->lazyLoad(),
    ],
    'location' => [
        Location::where('options_page', '7g-theme-settings'),
    ],
];
