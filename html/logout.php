<?php

declare(strict_types=1);

require_once(__DIR__ . '/init.php');

use DrlArchive\Config;
use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\userManagement\logoutUser\LogoutUserRequest;
use DrlArchive\core\interactors\userManagement\logoutUser\LogoutUserResponse;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\userManagement\LogoutUserFactory;

if (empty($_SESSION[Implementation::SESSION_AUTH_LOGGED_IN])) {
    header('Location: /index.php');
    exit;
}

$presenter = new class implements PresenterInterface {
    public function send(?Response $response = null): void
    {
        header("Location: {$response->getData()[LogoutUserResponse::DATA_REDIRECT_TO]}");
    }
};

$request = new LogoutUserRequest();

$referer = parse_url($_SERVER['HTTP_REFERER']);

if ($referer[Implementation::REFERER_HOST] === Config::HOST_NAME) {
    $request->setRedirectTo($referer[Implementation::REFERER_PATH]);
} else {
    $request->setRedirectTo('/index.php');
}

$useCase = (new LogoutUserFactory())->create(
    $presenter,
    $request
);

$useCase->execute();
