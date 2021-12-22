<?php

declare(strict_types=1);

use DrlArchive\Config;
use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\userManagement\loginUser\LoginUserRequest;
use DrlArchive\core\interactors\userManagement\loginUser\LoginUserResponse;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\userManagement\LoginUserFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;

require_once __DIR__ . '/../public/init.php';

if (isset($_SESSION[Implementation::SESSION_AUTH_LOGGED_IN])) {
    header('Location: /index.php');
    exit;
}

$presenter = new class extends AbstractTwigPagePresenter {
    public function send(?Response $response = null): void
    {
        parent::send($response);

        if ($response->getLoggedInUser() !== null) {
            if (!empty($response->getData()[LoginUserResponse::DATA_REDIRECT_TO])) {
                $nextPage = $response->getData()[LoginUserResponse::DATA_REDIRECT_TO];
            } else {
                $nextPage = '/index.php';
            }
            unset($_SESSION[Implementation::SESSION_REDIRECT_TO]);
            header('Location: ' . $nextPage);
            exit;
        }

        $this->dataForTemplate[self::NAV][self::NAV_HIGHLIGHTED] =
            Implementation::NAV_HIGHLIGHT_LOGIN;
        if ($response->getStatus() === Response::STATUS_FORBIDDEN) {
            $this->dataForTemplate[self::ERROR] = [
                self::ERROR_TYPE => Implementation::ALERT_DANGER,
                self::ERROR_MESSAGE => 'Username or password incorrect. Please try again'
            ];
            $this->dataForTemplate[self::USER][self::USER_EMAIL_ADDRESS] =
                $response->getData()[LoginUserResponse::DATA_EMAIL_ADDRESS];
        } elseif ($response->getStatus() !== Response::STATUS_SUCCESS) {
            $this->dataForTemplate[self::ERROR] = [
                self::ERROR_TYPE => Implementation::ALERT_DANGER,
                self::ERROR_MESSAGE => 'Something went wrong, please try again later',
            ];
            $this->dataForTemplate[self::USER][self::USER_EMAIL_ADDRESS] =
                $response->getData()[LoginUserResponse::DATA_EMAIL_ADDRESS];
        }
        try {
            $this->twig->display(
                'login.twig',
                $this->dataForTemplate
            );
        } catch (Throwable $e) {
            include __DIR__ . '/templates/failed.public';
        }
    }
};

$request = new LoginUserRequest();

$referer = parse_url($_SERVER['HTTP_REFERER']);

if (empty($_SESSION[Implementation::SESSION_REDIRECT_TO])) {
    if ($referer[Implementation::REFERER_HOST] === Config::HOST_NAME) {
        $_SESSION[Implementation::SESSION_REDIRECT_TO] =
            $referer[Implementation::REFERER_PATH];
    } else {
        $_SESSION[Implementation::SESSION_REDIRECT_TO] = '/index.php';
    }
}
$request->setRedirectTo($_SESSION[Implementation::SESSION_REDIRECT_TO]);

if (isset($_POST['password'], $_POST['email-address'])) {
    $request->setPassword($_POST['password']);
    $request->setEmailAddress($_POST['email-address']);
}

$useCase = (new LoginUserFactory())->create(
    $presenter,
    $request
);
$useCase->execute();
