<?php

/**
 * 
 */
function render_view(string $template, array $content = []): string
{
    $page = file_get_contents(filename: VIEW_FOLDER . $template . '.html');

    load_page_content($page, $content);

    return $page;
}

/**
 * 
 */
function load_page_content(string &$page, array $content): void
{
    foreach ($content as $placeholder => $value) {
        $page = str_replace(
            search: '{{' . $placeholder . '}}',
            replace: $value,
            subject: $page
        );
    }
}