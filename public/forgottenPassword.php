<?php

declare(strict_types=1);

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\forgottenPassword\ForgottenPassword;
use DrlArchive\core\interactors\pages\forgottenPassword\ForgottenPasswordRequest;
use DrlArchive\core\interactors\pages\forgottenPassword\ForgottenPasswordResponse;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\pages\ForgottenPasswordFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;

require_once __DIR__ . '/init.php';

if (isset($_SESSION[Implementation::SESSION_AUTH_LOGGED_IN])) {
    header('Location: /index.php');
    exit;
}

$presenter = new class extends AbstractTwigPagePresenter {
    private const TOKEN = 'token';
    private const SELECTOR = 'selector';
    private const FORM_VALUES = 'formValues';

    public function send(?Response $response = null): void
    {
        parent::send($response);

        $data = $response->getData();
        $this->dataForTemplate[self::NAV][self::NAV_HIGHLIGHTED] =
            Implementation::NAV_HIGHLIGHT_HOME;
        $this->dataForTemplate[self::TEMPLATES][self::TEMPLATES_BODY] =
            $data[ForgottenPasswordResponse::DATA_TEMPLATE];

        if ($response->getStatus() !== Response::STATUS_SUCCESS) {
            $this->dataForTemplate[self::ERROR] = [
                self::ERROR_MESSAGE => $response->getMessage(),
                self::ERROR_TYPE => Implementation::ALERT_DANGER,
            ];
        }

        if (
            $response->getMessage() ===
            ForgottenPassword::PASSWORD_RESET_SUCCESSFULLY
            || $response->getMessage() ===
            ForgottenPassword::EMAIL_SENT_SUCCESSFULLY
        ) {
            $this->dataForTemplate[self::ERROR] = [
                self::ERROR_MESSAGE => $response->getMessage(),
                self::ERROR_TYPE => Implementation::ALERT_SUCCESS,
            ];
        }

        if (isset($data[ForgottenPasswordResponse::DATA_TOKEN])) {
            $this->dataForTemplate[self::FORM_VALUES] = [
                self::TOKEN =>
                    $data[ForgottenPasswordResponse::DATA_TOKEN],
                self::SELECTOR =>
                    $data[ForgottenPasswordResponse::DATA_SELECTOR],
            ];
        }

        $this->twig->display(
            'forgottenPassword.twig',
            $this->dataForTemplate
        );
    }
};

$request = new ForgottenPasswordRequest();

if (isset($_POST['email-address'])) {
    $request->setEmailAddress($_POST['email-address']);
} elseif (isset($_POST['password'], $_POST['token'], $_POST['selector'])) {
    $request->setSelector($_POST['selector']);
    $request->setToken($_POST['token']);
    $request->setNewPassword($_POST['password']);
} elseif (isset($_GET['t'], $_GET['s'])) {
    $request->setToken($_GET['t']);
    $request->setSelector($_GET['s']);
}

$useCase = (new ForgottenPasswordFactory())->create(
    $presenter,
    $request
);
$useCase->execute();
