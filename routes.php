<?php

$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'login';

match ($page) {
    'login' => do_login(),
    'register' => do_register(),
    default => do_not_found()
};
