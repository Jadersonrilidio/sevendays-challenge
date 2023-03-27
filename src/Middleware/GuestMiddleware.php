<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Middleware;

use Closure;
use Jayrods\ScubaPHP\Core\{Request, Router};
use Jayrods\ScubaPHP\Infrastructure\Auth;
use Jayrods\ScubaPHP\Middleware\Middleware;

class GuestMiddleware implements Middleware
{
    /**
     * 
     */
    private Auth $auth;

    /**
     * 
     */
    public function __construct()
    {
        $this->auth = new Auth();
    }

    /**
     * 
     */
    public function handle(Request $request, Closure $next): bool
    {
        if ($this->auth->authUser()) {
            Router::redirect();
        }

        return call_user_func($next, $request);
    }
}
