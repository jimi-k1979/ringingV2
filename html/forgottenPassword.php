<?php

declare(strict_types=1);

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\forgottenPassword\ForgottenPassword;
use DrlArchive\core\interactors\pages\forgottenPassword\ForgottenPasswordRequest;
use DrlArchive\implementation\factories\interactors\pages\ForgottenPasswordFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;

require_once __DIR__ . '/init.php';

if (isset($_SESSION['auth_logged_in'])) {
    header('Location: /index.php');
    exit;
}

$presenter = new class extends AbstractTwigPagePresenter {
    public function send(?Response $response = null): void
    {
        parent::send($response);

        $data = $response->getData();
        $this->dataForTemplate['nav']['highlighted'] = 'home';
        $this->dataForTemplate['templates']['body'] =
            $data[ForgottenPassword::DATA_FIELD_TEMPLATE];

        if ($response->getStatus() !== Response::STATUS_SUCCESS) {
            $this->dataForTemplate['error'] = [
                'message' => $response->getMessage(),
                'type' => 'alert-danger',
            ];
        }

        if (
            $response->getMessage() ===
            ForgottenPassword::PASSWORD_RESET_SUCCESSFULLY
            || $response->getMessage() ===
            ForgottenPassword::EMAIL_SENT_SUCCESSFULLY
        ) {
            $this->dataForTemplate['error'] = [
                'message' => $response->getMessage(),
                'type' => 'alert-success',
            ];
        }

        if (isset($data[ForgottenPassword::DATA_FIELD_TOKEN])) {
            $this->dataForTemplate['formValues'] = [
                'token' => $data[ForgottenPassword::DATA_FIELD_TOKEN],
                'selector' => $data[ForgottenPassword::DATA_FIELD_SELECTOR],
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
