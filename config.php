<?php

use Dotenv\Dotenv;

// Global constants definition
define('ROOT_DIR', __DIR__);
define('SLASH', DIRECTORY_SEPARATOR);
define('VIEW_FOLDER', __DIR__ . SLASH . 'view' . SLASH);
define('COMPONENT_FOLDER', VIEW_FOLDER . 'components' . SLASH);
define('DATA_LOCATION', __DIR__ . SLASH . 'data' . SLASH . 'users.json');

// Environment variables loading
$dotenv = Dotenv::createImmutable(
    paths: ROOT_DIR
);
$dotenv->load();

define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost:8000');
define('ENVIRONMENT', $_ENV['ENVIRONMENT'] ?? 'production');
