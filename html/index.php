<?php

declare(strict_types=1);

require_once(__DIR__ . '/../vendor/autoload.php');

use DrlArchive\core\classes\Response;
use DrlArchive\implementation\factories\interactors\pages\indexPage\IndexPageFactory;
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
                ]
            );
        } catch (Throwable $e) {
            include __DIR__ . '/templates/failed.html';
        }
    }
};

$useCase = (new IndexPageFactory())->create(
    $presenter
);

$useCase->execute();

