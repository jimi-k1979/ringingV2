<?php

declare(strict_types=1);

require_once(__DIR__ . '/init.php');

use DrlArchive\Config;
use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\userManagement\logoutUser\LogoutUserRequest;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\implementation\factories\interactors\userManagement\LogoutUserFactory;

if (empty($_SESSION['auth_logged_in'])) {
    header('Location: /index.php');
    exit;
}

$presenter = new class implements PresenterInterface {
    public function send(?Response $response = null): void
    {
        header("Location: {$response->getData()['redirectTo']}");
    }
};

$request = new LogoutUserRequest();

$referer = parse_url($_SERVER['HTTP_REFERER']);

if ($referer['host'] === Config::HOST_NAME) {
    $request->setRedirectTo($referer['path']);
} else {
    $request->setRedirectTo('/index.php');
}

$useCase = (new LogoutUserFactory())->create(
    $presenter,
    $request
);

$useCase->execute();
