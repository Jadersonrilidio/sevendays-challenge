<?php

auth_user() ? auth_routes() : guest_routes();

/**
 * 
 */
function guest_routes()
{
    $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'login';

    match ($page) {
        'login' => do_login(),
        'register' => do_register(),
        'mail-validation' => do_validation(),
        'forget-password' => do_forget_password(),
        'change-password' => do_change_password(),
        default => do_not_found()
    };
}

/**
 * 
 */
function auth_routes()
{
    $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'home';

    match ($page) {
        'home' => do_home(),
        'logout' => do_logout(),
        'delete-account' => do_delete_account(),
        default => do_home()
    };
}
