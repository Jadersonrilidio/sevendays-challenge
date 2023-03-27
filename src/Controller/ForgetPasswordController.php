<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Controller\Traits\SSLEncryption;
use Jayrods\ScubaPHP\Core\{Request, Response, Router, View};
use Jayrods\ScubaPHP\Entity\User;
use Jayrods\ScubaPHP\Repository\JsonUserRepository;
use Jayrods\ScubaPHP\Service\MailService;
use Jayrods\ScubaPHP\Utils\FlashMessage;

class ForgetPasswordController extends Controller
{
    use SSLEncryption;

    /**
     * 
     */
    private JsonUserRepository $userRepository;

    // /**
    //  * 
    //  */
    // private ForgetPasswordValidator $forgetPasswordValidator;

    /**
     * 
     */
    private MailService $mail;

    /**
     * 
     */
    public function __construct(View $view, FlashMessage $flashMsg)
    {
        parent::__construct($view, $flashMsg);

        // $this->forgetPasswordValidator = new ForgetPasswordValidator($flashMsg);
        $this->userRepository = new JsonUserRepository();
        $this->mail = new MailService();
    }

    /**
     * 
     */
    public function index(Request $request): Response
    {
        $content = $this->view->renderView(
            template: 'forget_password',
            content: array(
                'status' => '',
                'email-value' => '',
                'email-errors' => '',
            )
        );

        $page = $this->view->renderlayout('Forget Password', $content);

        return new Response($page);
    }

    /**
     * 
     */
    public function sendmail(Request $request): Response
    {
        $user = $this->userRepository->findByEmail(
            email: $request->postVars('email')
        );

        if (!$user instanceof User) {
            $user = new User(
                name: 'not registered user',
                email: 'not.registered.user@example.com'
            );
        }

        $token = $this->SSLCrypt(
            data: $user->email() . '=' . time()
        );

        $link = APP_URL . SLASH . "change-password?token=$token";

        $this->mail->sendMail(
            to: $user->email(),
            name: $user->name(),
            subject: 'Scuba PHP define new password.',
            body: "Hi there! Click on the following link to define your account password: $link."
        );

        $this->flashMsg->set(array(
            'status-class' => 'mensagem-sucesso',
            'status-message' => 'Email sent, please check your mail box to retrieve your password.'
        ));

        Router::redirect('login');
        exit;
    }
}
