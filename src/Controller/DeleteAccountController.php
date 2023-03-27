<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Core\{Request, Response, Router, View};
use Jayrods\ScubaPHP\Entity\User;
use Jayrods\ScubaPHP\Infrastructure\Auth;
use Jayrods\ScubaPHP\Infrastructure\Validation\DeleteAccountValidator;
use Jayrods\ScubaPHP\Repository\JsonUserRepository;
use Jayrods\ScubaPHP\Utils\FlashMessage;

class DeleteAccountController extends Controller
{
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
    public function deleteAccount(Request $request): Response
    {
        $user = $this->auth->authUser();

        if (!$this->isValidUser($user)) {
            Router::redirect();
        }

        $result = $this->userRepository->remove($user);

        if (!$this->userRemoved($result) or !$this->logoutSucceed()) {
            Router::redirect();
        }

        $this->flashMsg->set(array(
            'status-class' => 'mensagem-sucesso',
            'status-message' => 'User account deleted with success.'
        ));

        Router::redirect('login');
        exit;
    }

    /**
     * 
     */
    private function isValidUser(User|false $user): bool
    {
        if (!$user instanceof User) {
            $this->flashMsg->set(array(
                'error-message' => 'Error: not possible to delete user account.',
            ));

            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function userRemoved(int|bool $removalResult): bool
    {
        if (!$removalResult) {
            $this->flashMsg->set(array(
                'error-message' => 'Error: not possible to delete user account.',
            ));

            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function logoutSucceed(): bool
    {
        if (!$this->auth->authLogout()) {
            $this->flashMsg->set(array(
                'error-message' => 'Error: not possible to logout user.',
            ));

            return false;
        }

        return true;
    }
}