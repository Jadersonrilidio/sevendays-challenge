<?php

$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'register';

match ($page) {
    'home' => do_home(),
    'login' => do_login(),
    'register' => do_register(),
    'mail-validation' => do_validation(),
    default => do_not_found()
};
