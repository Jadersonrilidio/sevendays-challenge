<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

require_once __DIR__ . SLASH . 'crypt.php';
require_once __DIR__ . SLASH . 'mail.php';
require_once __DIR__ . SLASH . 'crud.php';
require_once __DIR__ . SLASH . 'validation.php';
require_once __DIR__ . SLASH . 'auth.php';
require_once __DIR__ . SLASH . 'view.php';
require_once __DIR__ . SLASH . 'controller.php';
require_once __DIR__ . SLASH . 'routes.php';

/**
 * Dump and exit.
 * 
 * @param mixed $arguments All sort of values/objects user wants to dump.
 * 
 * @return void
 */
function dae(mixed ...$arguments): void
{
    var_dump(...$arguments);
    exit(PHP_EOL . "PROCESS ENDED" . PHP_EOL . PHP_EOL);
}
