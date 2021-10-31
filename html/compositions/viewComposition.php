<?php

declare(strict_types=1);

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\viewComposition\ViewCompositionRequest;
use DrlArchive\core\interactors\pages\viewComposition\ViewCompositionResponse;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\pages\ViewCompositionFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;

require_once __DIR__ . '/../init.php';

$presenter = new class extends AbstractTwigPagePresenter {
    private const COMPOSITION = 'composition';
    private const CONSTRUCTION = 'construction';
    private const TWO_COLUMN_ROWS = 'twoColumnRows';
    private const FOUR_COLUMN_ROWS = 'fourColumnRows';
    private const SIX_COLUMN_ROWS = 'sixColumnRows';

    public function send(?Response $response = null): void
    {
        parent::send($response);
        $this->dataForTemplate[self::NAV][self::NAV_HIGHLIGHTED] =
            Implementation::NAV_HIGHLIGHT_COMPOSITIONS;

        if ($response->getStatus() !== Response::STATUS_SUCCESS) {
            echo $response->getMessage();
        } else {
            $numberOfChanges =
                $response->getData()[ViewCompositionResponse::DATA_NUMBER_OF_CHANGES];

            $this->dataForTemplate[self::COMPOSITION] = $response->getData();
            $this->dataForTemplate[self::CONSTRUCTION] = [
                self::TWO_COLUMN_ROWS => (int)ceil($numberOfChanges / 2),
                self::FOUR_COLUMN_ROWS => (int)ceil($numberOfChanges / 4),
                self::SIX_COLUMN_ROWS => (int)ceil($numberOfChanges / 6),
            ];
            try {
                $this->twig->display(
                    'compositions/viewComposition.twig',
                    $this->dataForTemplate
                );
            } catch (Throwable $e) {
                echo $e->getMessage();
                die();
            }
        }
    }
};

$request = new ViewCompositionRequest();
$request->setCompositionId((int)$_GET['id']);
if ($_GET['direction'] === 'down') {
    $request->setUpChanges(false);
}

$useCase = (new ViewCompositionFactory())->create(
    $presenter,
    $request
);
$useCase->execute();
