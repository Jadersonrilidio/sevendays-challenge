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
function do_forget_password(array $data = [], int $httpCode = 200): void
{
    $_SERVER['REQUEST_METHOD'] === 'POST' ? forget_password_post() : forget_password_get();
}

/**
 * 
 */
function forget_password_get(array $data = [], int $httpCode = 200): void
{
    echo render_view(
        template: 'forget_password',
        content: array(
            'status' => '',
            'email-value' => '',
            'email-error-message' => '',
        )
    );
}

/**
 * 
 */
function forget_password_post(array $data = [], int $httpCode = 200): void
{
    if (isset($_POST['person'])) {
        $data = validatePostForgetPassword($_POST['person']);

        if (!$user = searchUserByEmail($data['email']['value'])) {
            $user = new StdClass();
            $user->name = 'Not registered email';
            $user->email = 'fakemail@example.com';
        }

        $date = (new DateTime())->format('Y-m-d');
        $tokenData = base64_encode($user->email . '=' . $date);

        $token = ssl_crypt(data: $tokenData);
        $link = APP_URL . SLASH . "?page=change-password&token=$token";

        send_mail(
            to: $user->email,
            name: $user->name,
            subject: 'Scuba PHP define new password.',
            body: "Hi there! Click on the following link to define your account password: $link."
        );

        header("Location: " . SLASH . "?page=login&from=forget-password");

        return;
    }

    forget_password_get(
        data: $data,
        httpCode: $httpCode
    );
}

/**
 * 
 */
function do_change_password(array $data = [], int $httpCode = 200): void
{
    $_SERVER['REQUEST_METHOD'] === 'POST' ? change_password_post() : change_password_get();
}

/**
 * 
 */
function change_password_get(array $data = [], int $httpCode = 200): void
{
    echo render_view(
        template: 'change_password',
        content: array(
            'status' => render_status_message($data['status'] ?? null),
            'password-error-message' => render_error_message($data['password'] ?? null),
            'password-confirm-error-message' => render_error_message($data['password-confirm'] ?? null),
            'token-value' => $data['token']['value'] ?? get_token(),
            'password-value' => $data['password']['value'] ?? '',
            'password-confirm-value' => $data['password-confirm']['value'] ?? '',
        )
    );
}

/**
 * 
 */
function get_token(): string
{
    return filter_input(INPUT_GET, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
}

/**
 * 
 */
function change_password_post(array $data = [], int $httpCode = 200): void
{
    if (isset($_POST['person'])) {
        $data = validatePostChangePassword($_POST['person']);

        if ($data['status']['valid'] === true) {
            $token = ssl_decrypt($data['token']['value']);
            $tokenArray = explode('=', base64_decode($token));
            
            $email = $tokenArray[0];
            $requestDate = new DateTime($tokenArray[1]);
            
            $today = new DateTime('now');
            $tomorrow = $today->modify("+1 day");
            
            if ($requestDate > $tomorrow) {
                header("Location: /?page=login&from=change-password");
            }
            
            if ($user = searchUserByEmail($email)) {
                $user->password = password_hash(
                    password: $data['password']['value'],
                    algo: PASSWORD_ARGON2ID
                );
                
                $result = crud_update(currentUser: $user);
                // dae($data, $token, $tokenArray, $user, $result);

                header("Location: " . SLASH . "?page=login&from=change-password");
                return;
            }
        }
    }

    change_password_get(
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
function do_delete_account(array $data = [], int $httpCode = 200): void
{
    crud_delete(currentUser: auth_user());

    unset($_SESSION['user']);

    http_response_code(response_code: 200);

    header("Location: " . SLASH . "?page=login&from=delete");
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

            crud_create(user: $user);

            $token = ssl_crypt(data: $user->email);
            $link = APP_URL . SLASH . "?page=mail-validation&token=$token";

            send_mail(
                to: $user->email,
                name: $user->name,
                subject: 'Scuba PHP account verification.',
                body: "Hi there! Click on the following link to verify your account: $link."
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
    is_from($data);

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
function is_from(array &$data): void
{
    if (isset($_GET['from']) and $_GET['from'] === 'register') {
        $data['status'] = array(
            'class' => 'mensagem-sucesso',
            'message' => "User sucessfully registered.<br>Please confirm your email before proceed."
        );
    } elseif (isset($_GET['from']) and $_GET['from'] === 'forget-password') {
        $data['status'] = array(
            'class' => 'mensagem-sucesso',
            'message' => "Forget password email sent."
        );
    } elseif (isset($_GET['from']) and $_GET['from'] === 'change-password') {
        $data['status'] = array(
            'class' => 'mensagem-sucesso',
            'message' => "Password redefined with success."
        );
    } elseif (isset($_GET['from']) and $_GET['from'] === 'home') {
        $data['status'] = array(
            'class' => 'mensagem-sucesso',
            'message' => "User logged out."
        );
    } elseif (isset($_GET['from']) and $_GET['from'] === 'delete') {
        $data['status'] = array(
            'class' => 'mensagem-sucesso',
            'message' => "User account deleted with success."
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
