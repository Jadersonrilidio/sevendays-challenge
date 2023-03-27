<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Core\{Request, Response};

class MaintenanceController extends Controller
{
    /**
     * 
     */
    public function index(Request $request): Response
    {
        $content = $this->view->renderView(template: 'maintenance');

        $page = $this->view->renderlayout('App Maintenance', $content);

        return new Response(
            content: $page,
            httpCode: 200
        );
    }
}
