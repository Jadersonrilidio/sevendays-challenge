<?php

/**
 * 
 */
function render_view(string $template): string
{
    return file_get_contents(
        filename: VIEW_FOLDER . $template . '.html'
    );
}
