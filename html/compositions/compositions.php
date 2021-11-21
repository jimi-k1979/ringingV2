<?php

declare(strict_types=1);

use DrlArchive\core\classes\Response;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\pages\CompositionPageFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;

require_once __DIR__ . '/../init.php';

$presenter = new class extends AbstractTwigPagePresenter {
    private const COMPOSITIONS = 'compositions';

    public function send(?Response $response = null): void
    {
        parent::send($response);
        $this->dataForTemplate[self::NAV][self::NAV_HIGHLIGHTED] =
            Implementation::NAV_HIGHLIGHT_COMPOSITIONS;

        if ($response->getStatus() !== Response::STATUS_SUCCESS) {
            echo $response->getMessage();
        } else {
            $this->dataForTemplate[self::COMPOSITIONS] = $response->getData();

            try {
                $this->twig->display(
                    'compositions/compositions.twig',
                    $this->dataForTemplate
                );
            } catch (Throwable $e) {
                echo $e->getMessage();
                die();
            }
        }
    }
};

$useCase = (new CompositionPageFactory())->create(
    $presenter
);
$useCase->execute();
