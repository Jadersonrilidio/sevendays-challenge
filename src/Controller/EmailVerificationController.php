<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Controller\Traits\SSLEncryption;
use Jayrods\ScubaPHP\Core\{Request, Response, Router, view};
use Jayrods\ScubaPHP\Entity\User;
use Jayrods\ScubaPHP\Repository\{JsonUserRepository, UserRepository};
use Jayrods\ScubaPHP\Utils\FlashMessage;

class EmailVerificationController extends Controller
{
    use SSLEncryption;

    /**
     * 
     */
    private UserRepository $userRepository;

    /**
     * 
     */
    public function __construct(view $view, FlashMessage $flashMsg)
    {
        parent::__construct($view, $flashMsg);

        $this->userRepository = new JsonUserRepository();
    }

    /**
     * 
     */
    public function verifyEmail(Request $request): Response
    {
        $token = $request->queryParams('token');

        $email = $this->SSLDecrypt($token);

        $user = $this->userRepository->findByEmail($email);

        $userEmailNotFound = $this->userEmailNotFound(
            user: $user
        );

        $emailAlreadyVerified = $this->emailAlreadyVerified(
            user: $user
        );
        
        if (!$userEmailNotFound or !$emailAlreadyVerified) {
            Router::redirect('login');
        }

        $this->flashMsg->set([
            'status-class' => 'mensagem-sucesso',
            'status-message' => 'Email verified with success. Proceed to login.'
        ]);

        $user->verify();

        $this->userRepository->update($user);

        Router::redirect('login');
        exit;
    }

    /**
     * 
     */
    private function userEmailNotFound(User|bool $user): bool
    {
        if (!$user instanceof User) {
            $this->flashMsg->set([
                'status-class' => 'mensagem-erro',
                'status-message' => 'Error on email verification: User email not found.'
            ]);

            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function emailAlreadyVerified(User $user): bool
    {
        if ($user->verified()) {
            $this->flashMsg->set([
                'status-class' => 'mensagem-erro',
                'status-message' => 'Email is already verified.'
            ]);

            return false;
        }

        return true;
    }
}
