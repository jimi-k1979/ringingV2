<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\forgottenPassword;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\EmailMessageEntity;
use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\Exceptions\AuthenticationException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\managers\AuthenticationManagerInterface;
use DrlArchive\core\interfaces\managers\EmailClientInterface;
use DrlArchive\Config;

/**
 * Class ForgottenPassword
 * @package DrlArchive\core\interactors\pages\forgottenPassword
 * @property ForgottenPasswordRequest $request
 */
class ForgottenPassword extends Interactor
{
    public const DATA_FIELD_TEMPLATE = 'template';
    public const DATA_FIELD_TOKEN = 'token';
    public const DATA_FIELD_SELECTOR = 'selector';

    public const TEMPLATE_GET_EMAIL = 'getEmail';
    public const TEMPLATE_GET_NEW_PASSWORD = 'getNewPassword';

    public const EMAIL_SENT_SUCCESSFULLY = 'Request email sent';
    public const PASSWORD_RESET_SUCCESSFULLY = 'Password reset successful';

    private EmailClientInterface $emailClient;
    private string $template = self::TEMPLATE_GET_EMAIL;
    private string $message = '';

    public function setEmailClient(EmailClientInterface $client): void
    {
        $this->emailClient = $client;
    }

    public function execute(): void
    {
        try {
            $this->processRequest();
            $this->createResponse();
        } catch (\Throwable $e) {
            $this->createFailureResponse($e);
        }

        $this->sendResponse();
    }

    /**
     * @throws CleanArchitectureException
     */
    private function processRequest(): void
    {
        if (!empty($this->request->getEmailAddress())) {
            $this->requestPasswordReset();
        } elseif (
            !empty($this->request->getSelector())
            && !empty($this->request->getToken())
        ) {
            if (empty($this->request->getNewPassword())) {
                $this->verifyResetLink();
            } else {
                $this->changePassword();
            }
        } elseif (
            !empty($this->request->getSelector())
            || !empty($this->request->getToken())
        ) {
            throw new AuthenticationException(
                'Incomplete reset data'
            );
        }
    }

    private function requestPasswordReset()
    {
        $emailMessage = new EmailMessageEntity();
        $emailMessage->addRecipientAddress(
            $this->request->getEmailAddress()
        );
        $emailMessage->setSubject(
            'Devon Ringing Archive Password Reset Request'
        );

        $host = Config::HOST_NAME;
        try {
            $resetDetails = $this->authenticationManager
                ->requestPasswordReset(
                    $this->request->getEmailAddress()
                );
            $emailMessage->setMessageBody(
                <<<message
Someone has requested a password reset for this email address. To reset your 
password simply click on the link below, or copy and paste it into you browser
where you will be taken to a page where you can reset your password.

http://{$host}/forgottenPassword.php?t={$resetDetails['token']}&s={$resetDetails['selector']}
message
            );
        } catch (\Throwable $e) {
            $emailMessage->setMessageBody(
                <<<message
You have tried to reset the password for this email address, but it does not 
exist in our database.
message
            );
        }

        $this->emailClient->sendMessage($emailMessage);
        $this->message = self::EMAIL_SENT_SUCCESSFULLY;
    }

    /**
     * @throws CleanArchitectureException
     */
    private function verifyResetLink(): void
    {
        $this->authenticationManager->verifyResetAttempt(
            $this->request->getSelector(),
            $this->request->getToken()
        );
        $this->template = self::TEMPLATE_GET_NEW_PASSWORD;
    }

    /**
     * @throws CleanArchitectureException
     */
    private function changePassword(): void
    {
        $user = new UserEntity();
        $user->setPassword($this->request->getNewPassword());
        $this->authenticationManager->completeResetAttempt(
            $user,
            $this->request->getSelector(),
            $this->request->getToken()
        );
        $this->message = self::PASSWORD_RESET_SUCCESSFULLY;
    }

    private function createResponse(): void
    {
        $dataArray = [
            self::DATA_FIELD_TEMPLATE => $this->template,
        ];

        if ($this->template === self::TEMPLATE_GET_NEW_PASSWORD) {
            $dataArray[self::DATA_FIELD_TOKEN] =
                $this->request->getToken();
            $dataArray[self::DATA_FIELD_SELECTOR] =
                $this->request->getSelector();
        }

        $this->response = new ForgottenPasswordResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setData($dataArray);
        $this->response->setMessage($this->message);
    }

    private function createFailureResponse(\Throwable $e): void
    {
        $this->response = new ForgottenPasswordResponse();
        $this->response->setData(
            [
                self::DATA_FIELD_TEMPLATE => self::TEMPLATE_GET_EMAIL
            ]
        );

        if ($e instanceof AuthenticationException) {
            $this->response->setStatus(
                Response::STATUS_UNAUTHORISED
            );
            $this->response->setMessage(
                'Incorrect data provided, have you got the correct link?'
            );
        } elseif (
            $e->getCode() ===
            AuthenticationManagerInterface::TOKEN_EXPIRED_EXCEPTION
        ) {
            $this->response->setStatus(
                Response::STATUS_BAD_REQUEST
            );
            $this->response->setMessage(
                'Password reset token expired, you need to start a new rest request'
            );
        } else {
            if (
                !empty($this->request->getToken())
                && !empty($this->request->getSelector())
            ) {
                $this->response->setData(
                    [
                        self::DATA_FIELD_TEMPLATE =>
                            self::TEMPLATE_GET_NEW_PASSWORD,
                        self::DATA_FIELD_SELECTOR =>
                            $this->request->getSelector(),
                        self::DATA_FIELD_TOKEN =>
                            $this->request->getToken(),
                    ]
                );
            }
            $this->response->setStatus(Response::STATUS_BAD_REQUEST);
            $this->response->setMessage(
                "Unable to change password, please try again - {$e->getCode()}: {$e->getMessage()}"
            );
        }
    }


}
