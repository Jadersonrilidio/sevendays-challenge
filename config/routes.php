<?php

declare(strict_types=1);

return array(
    // web routes
    'GET|/' => [Jayrods\ScubaPHP\Controller\HomeController::class, 'index', ['auth']],
    'GET|/register' => [Jayrods\ScubaPHP\Controller\RegisterController::class, 'index', ['guest']],
    'POST|/register' => [Jayrods\ScubaPHP\Controller\RegisterController::class, 'register', ['guest']],
    'GET|/login' => [Jayrods\ScubaPHP\Controller\LoginController::class, 'index', ['guest']],
    'POST|/login' => [Jayrods\ScubaPHP\Controller\LoginController::class, 'login', ['guest']],
    'GET|/logout' => [Jayrods\ScubaPHP\Controller\LogoutController::class, 'logout', ['auth']],
    'GET|/delete-account' => [Jayrods\ScubaPHP\Controller\DeleteAccountController::class, 'deleteAccount', ['auth']],
    'GET|/forget-password' => [Jayrods\ScubaPHP\Controller\ForgetPasswordController::class, 'index', ['guest']],
    'POST|/forget-password' => [Jayrods\ScubaPHP\Controller\ForgetPasswordController::class, 'sendMail', ['guest']],
    'GET|/change-password' => [Jayrods\ScubaPHP\Controller\ChangePasswordController::class, 'index', ['guest']],
    'POST|/change-password' => [Jayrods\ScubaPHP\Controller\ChangePasswordController::class, 'alterPassword', ['guest']],
    'GET|/verify-email' => [Jayrods\ScubaPHP\Controller\EmailVerificationController::class, 'verifyEmail', ['guest']],

    // fallback route
    'fallback' => [Jayrods\ScubaPHP\Controller\NotFoundController::class, 'index'],

    // maintenance route
    // 'GET|/maintenance' => [Jayrods\ScubaPHP\Controller\MaintenanceController::class, 'index', []],

    // API routes
);
