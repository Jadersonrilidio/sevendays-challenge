<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Middleware;

use Closure;
use Jayrods\ScubaPHP\Core\{Request, Response, View};
use Jayrods\ScubaPHP\Middleware\Middleware;

class MaintenanceMiddleware implements Middleware
{
    /**
     * 
     */
    public function handle(Request $request, Closure $next): bool
    {
        $maintenance = env('MAINTENANCE', 'false');

        if ($maintenance === 'true') {
            $view = new View();

            $content = $view->renderView(template: 'maintenance');
            $page = $view->renderlayout('App Maintenance', $content);

            $response = new Response(
                content: $page,
                httpCode: 200
            );

            $response->sendResponse();
        }

        return call_user_func($next, $request);
    }
}
