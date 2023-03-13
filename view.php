<?php

/**
 * 
 */
function render_view($template)
{
    return file_get_contents(
        filename: VIEW_FOLDER . $template . 'view'
    );
}
