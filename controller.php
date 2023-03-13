<?php

/**
 * 
 */
function do_register()
{
    header(header: 'Content-Type: text/html',);
    http_response_code(response_code: 200);

    echo render_view(template: 'register');
}

/**
 * 
 */
function do_login()
{
    header(header: 'Content-Type: text/html',);
    http_response_code(response_code: 200);

    echo render_view(template: 'login');
}

/**
 * 
 */
function do_not_found()
{
    header(header: 'Content-Type: text/html',);
    http_response_code(response_code: 404);

    echo render_view(template: 'not_found');
}
