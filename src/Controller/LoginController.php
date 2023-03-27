<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Controller\Traits\PasswordHandler;
use Jayrods\ScubaPHP\Core\{Request, Response, Router, view};
use Jayrods\ScubaPHP\Entity\User;
use Jayrods\ScubaPHP\Infrastructure\Auth;
use Jayrods\ScubaPHP\Repository\JsonUserRepository;
use Jayrods\ScubaPHP\Utils\FlashMessage;

class LoginController extends Controller
{
    use PasswordHandler;

    /**
     * 
     */
    private JsonUserRepository $userRepository;

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

        $this->userRepository = new JsonUserRepository();
        $this->auth = new Auth();
    }

    /**
     * 
     */
    public function index(Request $request): Response
    {
        $statusComponent = $this->view->renderStatusComponent(
            statusClass: $this->flashMsg->get('status-class'),
            statusMessage: $this->flashMsg->get('status-message')
        );

        $emailErrorComponent = $this->view->renderErrorMessageComponent(
            errorMessages: $this->flashMsg->getArray('email-errors')
        );

        $passwordErrorComponent = $this->view->renderErrorMessageComponent(
            errorMessages: $this->flashMsg->getArray('password-errors')
        );

        $content = $this->view->renderView(
            template: 'login',
            content: [
                'status' => $statusComponent,
                'email-errors' => $emailErrorComponent,
                'password-errors' => $passwordErrorComponent,
                'email-value' => $this->flashMsg->get('email-value'),
                'password-value' => $this->flashMsg->get('password-value'),
            ]
        );

        $page = $this->view->renderLayout('Login', $content);

        return new Response($page);
    }

    /**
     * 
     */
    public function login(Request $request): Response
    {
        $user = $this->userRepository->findByEmail(
            email: $request->postVars('email')
        );

        $passwordCheck = $this->passwordVerify(
            password: $request->postVars('password'),
            hash: $user instanceof User ? $user->password() : ''
        );

        $validEmailAndPassword = $this->validEmailAndPassword(
            request: $request,
            passwordCheck: $passwordCheck
        );

        $emailIsVerified = $this->emailIsVerified(
            request: $request,
            user: $user
        );

        if (!$validEmailAndPassword or !$emailIsVerified) {
            Router::redirect('login');
        }

        if ($this->passwordNeedRehash($user->password())) {
            $this->userRepository->passwordRehash(
                user: $user,
                password: $request->postVars('password')
            );
        }

        $this->auth->authenticate($user);

        Router::redirect();
        exit;
    }

    /**
     * 
     */
    private function validEmailAndPassword(Request $request, bool $passwordCheck): bool
    {
        if (!$passwordCheck) {
            !$this->flashMsg->set([
                'status-class' => 'mensagem-erro',
                'status-message' => 'Invalid email or password.',
                'email-value' => $request->postVars('email'),
                'password-value' => $request->postVars('password'),
            ]);

            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function emailIsVerified(Request $request, User|bool $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (!$user->verified()) {
            $this->flashMsg->set([
                'status-class' => 'mensagem-erro',
                'status-message' => 'Email not verified.',
                'email-value' => $request->postVars('email'),
                'password-value' => $request->postVars('password'),
            ]);

            return false;
        }

        return true;
    }
}
