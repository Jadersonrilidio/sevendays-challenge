<?php

/**
 * 
 */
function crud_create(array $userData)
{
    $users = json_decode(file_get_contents(DATA_LOCATION));

    $users[] = $userData;

    file_put_contents(DATA_LOCATION, json_encode($users));
}
