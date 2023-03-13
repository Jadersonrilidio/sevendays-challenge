<?php

$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'login';

switch ($page) {
    case 'login':
        do_login();
        break;
    case 'register':
        do_register();
        break;
    default:
        do_not_found();
        break;
}
