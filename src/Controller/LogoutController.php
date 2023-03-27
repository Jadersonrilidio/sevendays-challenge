<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Core\{Request, Response, Router, View};
use Jayrods\ScubaPHP\Infrastructure\Auth;
use Jayrods\ScubaPHP\Utils\FlashMessage;

class LogoutController extends Controller
{
    /**
     * 
     */
    private Auth $auth;

    /**
     * 
     */
    public function __construct(View $view, FlashMessage $flashMsg)
    {
        parent::__construct($view, $flashMsg);

        $this->auth = new Auth();
    }

    /**
     * 
     */
    public function logout(Request $request): Response
    {
        if ($this->auth->authLogout()) {
            $this->flashMsg->set(array(
                'status-class' => 'mensagem-sucesso',
                'status-message' => 'User logged out.',
            ));

            Router::redirect('login');
        }

        Router::redirect();
        exit;
    }
}
