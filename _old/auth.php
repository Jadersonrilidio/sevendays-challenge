<?php

/**
 * 
 */
function authentication(string $email, string $password): bool
{
    $user = searchUserByEmail($email);

    $pswdCheck = password_verify(
        password: $password,
        hash: $user->password ?? ''
    );

    if ($pswdCheck and $user->verified) {
        $_SESSION['user'] = json_encode($user);

        return true;
    }

    return false;
}

/**
 * 
 */
function auth_user(): StdClass|false
{
    return isset($_SESSION['user']) ? json_decode($_SESSION['user']) : false;
}

/**
 * 
 */
function auth_logout(): void
{
    if (isset($_SESSION['user'])) {
        unset($_SESSION['user']);
    }
}
