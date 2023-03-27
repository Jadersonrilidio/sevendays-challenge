<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Core\{Request, Response};

class HomeController extends Controller
{
    /**
     * 
     */
    public function index(Request $request): Response
    {
        $content = $this->view->renderView(
            template: 'home',
            content: array(
                'status' => '',
                'user-name' => '',
                'user-email' => '',
                'error-message' => ''
            )
        );

        $page = $this->view->renderlayout('Home', $content);

        return new Response($page);
    }
}
