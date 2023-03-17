<?php

use Dotenv\Dotenv;

// Global constants definition
define('SLASH', DIRECTORY_SEPARATOR);
define('VIEW_FOLDER', __DIR__ . SLASH . 'view' . SLASH);
define('DATA_LOCATION', __DIR__ . SLASH . 'data' . SLASH . 'users.json');
define('ROOT_DIR', __DIR__);
define('COMPONENT_FOLDER', __DIR__ . SLASH . 'view' . SLASH . 'components' . SLASH);

// Environment variables loading
$dotenv = Dotenv::createImmutable(
    paths: ROOT_DIR
);
$dotenv->load();

define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost:8000');
