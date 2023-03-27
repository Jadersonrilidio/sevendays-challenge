<?php

declare(strict_types=1);

return array(
    'maintenance' => Jayrods\ScubaPHP\Middleware\MaintenanceMiddleware::class,
    'session' => Jayrods\ScubaPHP\Middleware\SessionMiddleware::class,
    'auth' => Jayrods\ScubaPHP\Middleware\AuthMiddleware::class,
    'guest' => Jayrods\ScubaPHP\Middleware\GuestMiddleware::class,
);
