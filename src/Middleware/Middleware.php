<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Middleware;

use Closure;
use Jayrods\ScubaPHP\Core\Request;

interface Middleware
{
    /**
     * 
     */
    public function handle(Request $request, Closure $next): bool;
}
