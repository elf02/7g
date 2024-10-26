<?php

$fields = \SG\fields();

if ($fields->block_area) {
    echo \SG\get_block_area($fields->block_area);
}

?>