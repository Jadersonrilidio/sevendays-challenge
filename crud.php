<?php

/**
 * 
 */
function crud_create(array $userData): void
{
    $users = json_decode(file_get_contents(DATA_LOCATION), true);
    $users[] = $userData;

    file_put_contents(DATA_LOCATION, json_encode($users));
}

/**
 * 
 */
function searchUserByEmail(string $email): mixed
{
    $users = json_decode(file_get_contents(DATA_LOCATION));

    foreach ($users as $user) {
        if ($user->email === $email) {
            return $user;
        }
    }

    return false;
}

/**
 * 
 */
function email_exists(string $email): bool
{
    $users = json_decode(file_get_contents(DATA_LOCATION));

    foreach ($users as $user) {
        if ($user->email === $email) {
            return true;
        }
    }

    return false;
}

/**
 * 
 */
function userDto(array $data): array
{
    return array(
        'name' => $data['name']['value'],
        'email' => $data['email']['value'],
        'password' => password_hash(
            password: $data['password']['value'],
            algo: PASSWORD_ARGON2ID
        )
    );
}
