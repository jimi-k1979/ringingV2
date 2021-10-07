<?php

declare(strict_types=1);

use DrlArchive\Config;
use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\userManagement\loginUser\LoginUser;
use DrlArchive\core\interactors\userManagement\loginUser\LoginUserRequest;
use DrlArchive\implementation\factories\interactors\userManagement\LoginUserFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;

require_once __DIR__ . '/../html/init.php';

if (isset($_SESSION['auth_logged_in'])) {
    header('Location: /index.php');
    exit;
}

$presenter = new class extends AbstractTwigPagePresenter {
    public function send(?Response $response = null): void
    {
        parent::send($response);

        if ($response->getLoggedInUser() !== null) {
            if (!empty($response->getData()[LoginUser::DATA_REDIRECT_TO])) {
                $nextPage = $response->getData()[LoginUser::DATA_REDIRECT_TO];
            } else {
                $nextPage = '/index.php';
            }
            unset($_SESSION['redirectTo']);
            header('Location: ' . $nextPage);
            exit;
        }

        $this->dataForTemplate['nav']['highlighted'] = 'logIn';
        if ($response->getStatus() === Response::STATUS_FORBIDDEN) {
            $this->dataForTemplate['error'] = [
                'type' => 'alert-danger',
                'message' => 'Username or password incorrect. Please try again'
            ];
            $this->dataForTemplate['user']['emailAddress'] =
                $response->getData()[LoginUser::DATA_EMAIL_ADDRESS];
        } elseif ($response->getStatus() !== Response::STATUS_SUCCESS) {
            $this->dataForTemplate['error'] = [
                'type' => 'alert-danger',
                'message' => 'Something went wrong, please try again later',
            ];
            $this->dataForTemplate['user']['emailAddress'] =
                $response->getData()[LoginUser::DATA_EMAIL_ADDRESS];
        }
        try {
            $this->twig->display(
                'login.twig',
                $this->dataForTemplate
            );
        } catch (Throwable $e) {
            include __DIR__ . '/templates/failed.html';
        }
    }
};

$request = new LoginUserRequest();

$referer = parse_url($_SERVER['HTTP_REFERER']);

if (empty($_SESSION['redirectTo'])) {
    if ($referer['host'] === Config::HOST_NAME) {
        $_SESSION['redirectTo'] = $referer['path'];
    } else {
        $_SESSION['redirectTo'] = '/index.php';
    }
}
$request->setRedirectTo($_SESSION['redirectTo']);

if (isset($_POST['password'], $_POST['email-address'])) {
    $request->setPassword($_POST['password']);
    $request->setEmailAddress($_POST['email-address']);
}

$useCase = (new LoginUserFactory())->create(
    $presenter,
    $request
);
$useCase->execute();
