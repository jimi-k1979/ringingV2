<?php

declare(strict_types=1);

require_once(__DIR__ . '/init.php');

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\indexPage\IndexPageRequest;
use DrlArchive\implementation\factories\interactors\pages\IndexPageFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;

$presenter = new class extends AbstractTwigPagePresenter {
    public function send(?Response $response = null): void
    {
        parent::send($response);
        try {
            $this->twig->display(
                'index.twig',
                [
                    'loggedIn' => $this->loggedInStatus,
                    'previousStatus' => $response->getData()['previousStatus']
                ]
            );
        } catch (Throwable $e) {
            include __DIR__ . '/templates/failed.html';
        }
    }
};

$request = new IndexPageRequest();
if (isset($_SESSION['previousStatus'])) {
    $request->setPreviousStatus((int)$_SESSION['previousStatus']);
    unset($_SESSION['previousStatus']);
}

$useCase = (new IndexPageFactory())->create(
    $presenter,
    $request
);

$useCase->execute();

