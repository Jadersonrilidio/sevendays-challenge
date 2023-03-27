<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Controller\Traits\{PasswordHandler, SSLEncryption};
use Jayrods\ScubaPHP\Core\{Request, Response, Router, View};
use Jayrods\ScubaPHP\Entity\User;
use Jayrods\ScubaPHP\Controller\Validation\ChangePasswordValidator;
use Jayrods\ScubaPHP\Repository\JsonUserRepository;
use Jayrods\ScubaPHP\Utils\FlashMessage;

class ChangePasswordController extends Controller
{
    use PasswordHandler,
        SSLEncryption;

    /**
     * 
     */
    private const EXPIRATION_TIME = 86400;

    /**
     * 
     */
    private JsonUserRepository $userRepository;

    /**
     * 
     */
    private ChangePasswordValidator $changePasswordValidator;

    /**
     * 
     */
    public function __construct(View $view, FlashMessage $flashMsg)
    {
        parent::__construct($view, $flashMsg);

        $this->changePasswordValidator = new ChangePasswordValidator($flashMsg);
        $this->userRepository = new JsonUserRepository();
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

        $passwordErrorComponent = $this->view->renderErrorMessageComponent(
            errorMessages: $this->flashMsg->getArray('password-errors')
        );

        $passwordConfirmErrorComponent = $this->view->renderErrorMessageComponent(
            errorMessages: $this->flashMsg->getArray('password-confirm-errors')
        );

        $content = $this->view->renderView(
            template: 'change_password',
            content: array(
                'status' => $statusComponent,
                'password-errors' => $passwordErrorComponent,
                'password-confirm-errors' => $passwordConfirmErrorComponent,
                'token-value' => $request->queryParams('token'),
                'password-value' => $this->flashMsg->get('password-value'),
                'password-confirm-value' => $this->flashMsg->get('password-confirm-value'),
            )
        );

        $page = $this->view->renderlayout('Change Passoword', $content);

        return new Response($page);
    }

    /**
     * 
     */
    public function alterPassword(Request $request): Response
    {
        $this->changePasswordValidator->validate($request);

        $token = $this->SSLDecrypt($request->postVars('token'));
        $token = explode('=', $token);

        $email = $token[0];

        $requestTimestamp = $token[1];
        $today = time();
        $elapsedTime = $today - $requestTimestamp;

        if ($elapsedTime > self::EXPIRATION_TIME) {
            $this->flashMsg->set(array(
                'status-class' => 'mensagem-error',
                'status-message' => 'Change password token has expired.',
            ));

            Router::redirect('login');
        }

        $user = $this->userRepository->findByEmail($email);

        $updatedUser = new User(
            name: $user->name(),
            email: $user->email(),
            verified: $user->verified(),
            password: $this->passwordHash($request->postVars('password')),
        );

        if (!$this->userRepository->update($updatedUser)) {
            $this->flashMsg->set(array(
                'status-class' => 'mensagem-erro',
                'status-message' => 'Not possible to update user.',
            ));

            Router::redirect('login');
        }

        $this->flashMsg->set(array(
            'status-class' => 'mensagem-sucesso',
            'status-message' => 'Password redefined with sucess.',
        ));

        Router::redirect('login');
        exit;
    }
}
