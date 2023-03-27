<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Middleware;

use Closure;
use Jayrods\ScubaPHP\Core\Request;
use Jayrods\ScubaPHP\Middleware\Middleware;

class SessionMiddleware implements Middleware
{
    /**
     * 
     */
    public function handle(Request $request, Closure $next): bool
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        return call_user_func($next, $request);
    }
}
