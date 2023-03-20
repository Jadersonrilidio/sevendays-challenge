<?php

function crud_load(): array
{
    $users = file_get_contents(DATA_LOCATION);
    $users = json_decode($users);
    return $users;
}

function crud_flush(array $data): int|bool
{
    $data = json_encode($data);
    return file_put_contents(DATA_LOCATION, $data);
}

/**
 * 
 */
function crud_create(StdClass $user): int|bool
{
    $users = crud_load();
    $users[] = $user;

    return crud_flush($users);
}

/**
 * 
 */
function crud_update(object $currentUser): int|bool
{
    $users = crud_load();

    foreach ($users as $i => $user) {
        if ($user->email === $currentUser->email) {
            $users[$i] = $currentUser;

            return crud_flush($users);
        }
    }

    return false;
}

/**
 * @return StdClass[]|false
 */
function crud_select(): array|false
{
    return crud_load();
}

/**
 * 
 */
function crud_delete(StdClass $currentUser): int|bool
{
    $users = crud_load();

    foreach ($users as $i => $user) {
        if ($user->email === $currentUser->email) {
            unset($users[$i]);

            return crud_flush($users);
        }
    }

    return false;
}

/**
 * @return StdClass|false
 */
function searchUserByEmail(string $email): StdClass|false
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
 * @return StdClass
 */
function userDtoObject(string $name, string $email, string $password, bool $verified = false): StdClass
{
    $user = new stdClass();

    $user->name = $name;
    $user->email = $email;
    $user->verified = $verified;
    $user->password = password_hash(
        password: $password,
        algo: PASSWORD_ARGON2ID
    );

    return $user;
}
