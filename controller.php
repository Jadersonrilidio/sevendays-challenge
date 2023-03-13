<?php

/**
 * 
 */
function do_register(): void
{
    header(header: 'Content-Type: text/html',);
    http_response_code(response_code: 200);

    if (isset(
        $_POST['name'],
        $_POST['email'],
        $_POST['password'],
        $_POST['password-confirm']
    )) {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $passwordConfirm = filter_input(INPUT_POST, 'password-confirm', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (
            $name !== null
            and $email !== null
            and $password !== null
            and $password !== ''
            and $password === $passwordConfirm
        ) {
            $userData = array(
                'name' => $name,
                'email' => $email,
                'password' => password_hash(
                    password: $password,
                    algo: PASSWORD_ARGON2ID
                )
            );

            crud_create($userData);

            header("Location: http://localhost:8001/?page=login");
        }
    }

    echo render_view(template: 'register');
}

/**
 * 
 */
function do_login(): void
{
    header(header: 'Content-Type: text/html',);
    http_response_code(response_code: 200);

    echo render_view(template: 'login');
}

/**
 * 
 */
function do_not_found(): void
{
    header(header: 'Content-Type: text/html',);
    http_response_code(response_code: 404);

    echo render_view(template: 'not_found');
}
