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
function do_login(array $data = [], int $httpCode = 200): void
{
    isset($_POST['person']) ? login_post() : login_get();
}

/**
 * 
 */
function do_logout(array $data = [], int $httpCode = 200): void
{
    unset($_SESSION['user']);

    http_response_code(response_code: 200);

    header("Location: " . SLASH . "?page=login&from=home");
}

/**
 * 
 */
function do_validation(): void
{
    $statusData = verifyEmail();

    login_get(
        data: $statusData,
        httpCode: 200
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
function do_delete_account(array $data = [], int $httpCode = 200): void
{
    crud_delete(user: auth_user());

    unset($_SESSION['user']);

    http_response_code(response_code: 200);

    header("Location: " . SLASH . "?page=login&from=home");
}

/**
 * 
 */
function do_not_found(array $data = [], int $httpCode = 404): void
{
    http_response_code(response_code: $httpCode);

    echo render_view(
        template: 'not_found',
        content: $data
    );
}

/**
 * 
 */
function register_get(array $data = [], int $httpCode = 200)
{
    http_response_code(response_code: $httpCode);

    echo render_view(
        template: 'register',
        content: array(
            'status' => render_status_message($data['status'] ?? null),
            'name-error-message' => render_error_message($data['name'] ?? null),
            'email-error-message' => render_error_message($data['email'] ?? null),
            'password-error-message' => render_error_message($data['password'] ?? null),
            'password-confirm-error-message' => render_error_message($data['password-confirm'] ?? null),
            'name-value' => isset($data['name']['value']) ? $data['name']['value'] : '',
            'email-value' => isset($data['email']['value']) ? $data['email']['value'] : '',
            'password-value' => isset($data['password']['value']) ? $data['password']['value'] : '',
            'password-confirm-value' => isset($data['password-confirm']['value']) ? $data['password-confirm']['value'] : '',
        )
    );
}

function register_post(array $data = [], int $httpCode = 200)
{
    if (isset($_POST['person'])) {
        $data = validatePostRegisterUser($_POST['person']);

        if ($data['status']['valid'] === true) {
            $user = userDtoObject(
                name: $data['name']['value'],
                email: $data['email']['value'],
                password: $data['password']['value'],
                verified: false
            );

            crud_create_object(user: $user);

            $token = ssl_crypt(data: $user->email);
            $link = APP_URL . SLASH . "?page=mail-validation&token=$token";

            send_mail(
                to: $user->email,
                name: $user->name,
                subject: 'Scuba PHP account verification.',
                body: "Hi there!\n\nClick on the following link to verify your account: $link."
            );

            header("Location: " . SLASH . "?page=login&from=register");

            return;
        }
    }

    register_get(
        data: $data,
        httpCode: $httpCode
    );
}

/**
 * 
 */
function login_get(array $data = [], int $httpCode = 200)
{
    is_from_register($data);

    http_response_code(response_code: $httpCode);

    echo render_view(
        template: 'login',
        content: array(
            'status' => render_status_message($data['status'] ?? null),
            'email-error-message' => render_error_message($data['email'] ?? null),
            'password-error-message' => render_error_message($data['password'] ?? null),
            'email-value' => isset($data['email']['value']) ? $data['email']['value'] : '',
            'password-value' => isset($data['password']['value']) ? $data['password']['value'] : '',
        )
    );
}

/**
 * 
 */
function is_from_register(array &$data): void
{
    if (isset($_GET['from']) and $_GET['from'] === 'register') {
        $data['status'] = array(
            'class' => 'mensagem-sucesso',
            'message' => "User sucessfully registered.<br>Please confirm your email before proceed."
        );
    }
}

/**
 * 
 */
function login_post(array $data = [], int $httpCode = 200)
{
    $data = validatePostLoginUser($_POST['person']);

    if ($data['status']['valid'] === true) {
        $authenticated = authentication(
            email: $data['email']['value'],
            password: $data['password']['value']
        );

        if ($authenticated) {
            header("Location: " . SLASH . "?page=home&from=login");
            return;
        }

        $data['status'] = array(
            'valid' => false,
            'class' => 'mensagem-erro',
            'message' => 'Invalid email or password',
        );
    }

    login_get(
        data: $data,
        httpCode: $httpCode
    );
}

/**
 * 
 */
function home_get(array $data = [], int $httpCode = 200)
{
    $user = auth_user();

    is_from_login($data, $user);

    http_response_code(response_code: $httpCode);

    echo render_view(
        template: 'home',
        content: array(
            'status-class' => $data['status']['class'] ?? '',
            'status-message' => $data['status']['message'] ?? '',
            'user-name' => $user->name ?? '',
            'user-email' => $user->email ?? '',
            'error-message' => $data['error']['message'] ?? ''
        )
    );
}

/**
 * 
 */
function is_from_login(array &$data, StdClass $user): void
{
    if (isset($_GET['from']) and $_GET['from'] === 'login') {
        $data['status'] = array(
            'class' => 'mensagem-sucesso',
            'message' => rtrim("Welcome back, " . ($user->name ?? ''), ', ')
        );
    }
}
