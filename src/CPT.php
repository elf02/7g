<?php

namespace SG;

use SG\Attributes\Hook;
use SG\Contracts\Hookable;

class CPT implements Hookable
{
    #[Hook('init')]
    public function register_cpt(): void
    {
        foreach (glob(get_parent_theme_file_path('cpt/*.php')) as $cpt_path) {
            include $cpt_path;
        }
    }
}
