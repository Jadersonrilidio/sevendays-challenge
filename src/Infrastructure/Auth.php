<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Infrastructure;

use Jayrods\ScubaPHP\Controller\Traits\SSLEncryption;
use Jayrods\ScubaPHP\Entity\User;

class Auth
{
    use SSLEncryption;

    /**
     * 
     */
    private const SESSION_AUTH = 'user';

    /**
     * 
     */
    public function authenticate(User $user): bool
    {
        $sessionUser = new User(
            name: $user->name(),
            email: $user->email(),
            password: null,
            verified: $user->verified()
        );

        if (!$userData = $this->SSLCrypt($sessionUser)) {
            return false;
        }

        $_SESSION[self::SESSION_AUTH] = $userData;

        return true;
    }

    /**
     * 
     */
    public function authUser(): User|false
    {
        if ($session = $_SESSION[self::SESSION_AUTH] ?? false) {
            $userData = $this->SSLDecrypt($session);

            return new User(
                name: $userData->name,
                email: $userData->email,
                password: null,
                verified: $userData->verified
            );
        }

        return false;
    }

    /**
     * 
     */
    public function authLogout(): bool
    {
        if (isset($_SESSION[self::SESSION_AUTH])) {
            unset($_SESSION[self::SESSION_AUTH]);

            return true;
        }

        return false;
    }
}
