<?php

/**
 * 
 */
function crud_create(array $userData): bool
{
    try {
        $users = json_decode(file_get_contents(DATA_LOCATION), true);
        $users[] = $userData;

        file_put_contents(DATA_LOCATION, json_encode($users));
    } catch (Throwable $e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
        return false;
    }

    return true;
}

/**
 * 
 */
function crud_create_object(object $user): int|bool
{
    $users = json_decode(file_get_contents(DATA_LOCATION));
    $users[] = $user;

    return file_put_contents(DATA_LOCATION, json_encode($users));
}

/**
 * 
 */
function crud_update(object $user): bool
{
    try {
        $users = json_decode(file_get_contents(DATA_LOCATION));

        foreach ($users as $i => $item) {
            if ($item->email === $user->email) {
                $users[$i] = $user;
                file_put_contents(DATA_LOCATION, json_encode($users));
                break;
            }
        }
    } catch (Throwable $e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
        return false;
    }

    return true;
}

/**
 * @return StdClass[]|false
 */
function crud_select(): array|false
{
    try {
        return json_decode(file_get_contents(DATA_LOCATION));
    } catch (Throwable $e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
        return false;
    }
}

/**
 * 
 */
function crud_delete(object $user): bool
{
    try {
        $users = json_decode(file_get_contents(DATA_LOCATION));

        foreach ($users as $i => $item) {
            if ($item->email === $user->email) {
                unset($users[$i]);
                file_put_contents(DATA_LOCATION, json_encode($users));
                break;
            }
        }
    } catch (Throwable $e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
        return false;
    }

    return true;
}

/**
 * @return StdClass|false
 */
function searchUserByEmail(string $email): StdClass|false
{
    try {
        $users = json_decode(file_get_contents(DATA_LOCATION));

        foreach ($users as $user) {
            if ($user->email === $email) {
                return $user;
            }
        }
    } catch (Throwable $e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
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
        'verified' => $data['mail-validation']['value'] ?? false,
        'password' => password_hash(
            password: $data['password']['value'],
            algo: PASSWORD_ARGON2ID
        )
    );
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
