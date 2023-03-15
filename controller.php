<?php

/**
 * 
 */
function do_register(): void
{
    isset($_POST['person']) ? register_post() : register_get();
}

/**
 * 
 */
function register_get(array $data = array(), int $httpCode = 200)
{
    http_response_code(response_code: $httpCode);

    echo render_view(
        template: 'register',
        content: array(
            'status-class' => isset($data['status']['class']) ? $data['status']['class'] : '',
            'status-message' => isset($data['status']['message']) ? $data['status']['message'] : '',
            'name-error' => isset($data['name']['errors']) ? implode(' ', $data['name']['errors']) : '',
            'name-value' => isset($data['name']['value']) ? $data['name']['value'] : '',
            'email-error' => isset($data['email']['errors']) ? implode(' ', $data['email']['errors']) : '',
            'email-value' => isset($data['email']['value']) ? $data['email']['value'] : '',
            'password-error' => isset($data['password']['errors']) ? implode(' ', $data['password']['errors']) : '',
            'password-value' => isset($data['password']['value']) ? $data['password']['value'] : '',
            'password-confirm-error' => isset($data['password-confirm']['errors']) ? implode(' ', $data['password-confirm']['errors']) : '',
            'password-confirm-value' => isset($data['password-confirm']['value']) ? $data['password-confirm']['value'] : '',
        )
    );
}

function register_post(array $data = array(), int $httpCode = 200)
{
    if (isset($_POST['person'])) {
        $data = validatePostRegisterUser($_POST['person']);

        if ($data['status']['valid'] === true) {
            crud_create(userData: userDto($data));

        header("Location: /?page=login");
        header("Location: /?page=login&from=register");
        return;
        }
    }

    register_get(
        data: $data,
        httpCode:$httpCode
    );
}

/**
 * 
 */
function do_login(array $data = [], int $httpCode = 200): void
{
    isset($_POST['person']) ? login_post() : login_get();
}

/**
 * 
 */
function login_get(array $data = [], int $httpCode = 200)
{
    fromRegister($data);

    http_response_code(response_code: $httpCode);

    echo render_view(
        template: 'login',
        content: array(
            'status-class' => isset($data['status']['class']) ? $data['status']['class'] : '',
            'status-message' => isset($data['status']['message']) ? $data['status']['message'] : '',
            'email-error' => isset($data['email']['errors']) ? implode(' ', $data['email']['errors']) : '',
            'email-value' => isset($data['email']['value']) ? $data['email']['value'] : '',
            'password-error' => isset($data['password']['errors']) ? implode(' ', $data['password']['errors']) : '',
            'password-value' => isset($data['password']['value']) ? $data['password']['value'] : '',
        )
    );
}

/**
 * 
 */
function login_post(array $data = [], int $httpCode = 200)
{
    $data = validatePostLoginUser($_POST['person']);

    if ($data['status']['valid'] === true) {
        $user = searchUserByEmail($data['email']['value']);

        if (
            $user !== false
            and password_verify(
                password: $data['password']['value'],
                hash: $user->password
            )
        ) {
            $data = array(
                'status' => array(
                    'class' => 'mensagem-sucesso',
                    'message' => "Welcome back, {$user->name}"
                ),
                'user' => $user
            );

            do_home(data: $data, httpCode: 200);
            return;
        }

        $data['status']['valid'] = false;
        $data['status']['class'] = 'mensagem-erro';
        $data['status']['message'] = 'Invalid email or password';
    }

    login_get(
        data: $data,
        httpCode: $httpCode
    );
}

/**
 * 
 */
function do_home(array $data = [], int $httpCode = 200): void
{
    home_get($data, $httpCode);
}

/**
 * 
 */
function home_get(array $data = [], int $httpCode = 200)
{
    http_response_code(response_code: $httpCode);

    echo render_view(
        template: 'home',
        content: array(
            'status-class' => $data['status']['class'] ?? '',
            'status-message' => $data['status']['message'] ?? '',
            'user-name' => $data['user']->name ?? '',
            'user-email' => $data['user']->email ?? '',
            'error-message' => $data['error']['message'] ?? ''
            )
    );
}

/**
 * 
 */
function do_not_found(array $data = [], int $httpCode = 404): void
{
    http_response_code(response_code: $httpCode);

    echo render_view(
        template: 'not_found',
        content: array()
    );
}
