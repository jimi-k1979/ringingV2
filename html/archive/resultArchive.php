<?php

/**
 * @var Environment $twig
 */
declare(strict_types=1);

require_once __DIR__ . '/../init.php';

use DrlArchive\core\classes\Response;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\pages\ResultsArchiveFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;
use Twig\Environment;

$presenter = new class extends AbstractTwigPagePresenter {
    public function send(?Response $response = null): void
    {
        parent::send($response);
        $this->dataForTemplate[self::NAV][self::NAV_HIGHLIGHTED] =
            Implementation::NAV_HIGHLIGHT_ARCHIVE;

        $this->twig->display(
            'archive/eventSearch.twig',
            $this->dataForTemplate
        );
    }
};

$useCase = (new ResultsArchiveFactory())->create(
    $presenter
);
$useCase->execute();
