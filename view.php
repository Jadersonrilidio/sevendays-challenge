<?php

/**
 * 
 */
function render_view(string $template, array $content = []): string
{
    $page = file_get_contents(filename: VIEW_FOLDER . $template . '.html');
    load_content($page, $content);

    return $page;
}

/**
 * 
 */
function render_component(string $template, array $content = []): string
{
    $component = file_get_contents(filename: VIEW_FOLDER . 'components' . SLASH . $template . '.html');
    load_content($component, $content);

    return $component;
}

/**
 * 
 */
function load_content(string &$page, array $content): void
{
    foreach ($content as $placeholder => $value) {
        $page = str_replace(
            search: '{{' . $placeholder . '}}',
            replace: $value,
            subject: $page
        );
    }
}

/**
 * 
 */
function render_error_message(array $data = []): string
{
    if (!isset($data['errors'])) {
        return '';
    }

    return render_component(
        template: 'input_message',
        content: array(
            'error-message' => implode(' ', $data['errors'])
        )
    );
}

/**
 * 
 */
function render_status_message(array $statusData = []): string
{
    if (!isset($statusData['message'])) {
        return '';
    }

    return render_component(
        template: 'status',
        content: array(
            'status-class' => $statusData['class'],
            'status-message' => $statusData['message'],
        )
    );
}
