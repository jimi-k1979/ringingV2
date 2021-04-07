<?php

declare(strict_types=1);

require_once __DIR__ . '/init.php';

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\indexPage\IndexPageRequest;
use DrlArchive\implementation\factories\interactors\pages\IndexPageFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;

$presenter = new class extends AbstractTwigPagePresenter {
    public function send(?Response $response = null): void
    {
        parent::send($response);

        $this->dataForTemplate['previousStatus'] =
            $response->getData()['previousStatus'];
        $this->dataForTemplate['nav']['highlighted'] = 'home';

        try {
            $this->twig->display(
                'index.twig',
                $this->dataForTemplate
            );
        } catch (Throwable $e) {
            echo $e->getMessage();
            die();
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

