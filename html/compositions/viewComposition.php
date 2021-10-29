<?php

declare(strict_types=1);

use DrlArchive\core\classes\Response;
use DrlArchive\Implementation;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;

require_once __DIR__ . '/../init.php';

$presenter = new class extends AbstractTwigPagePresenter {
    private const COMPOSITION = 'composition';

    public function send(?Response $response = null): void
    {
        parent::send($response);
        $this->dataForTemplate[self::NAV][self::NAV_HIGHLIGHTED] =
            Implementation::NAV_HIGHLIGHT_COMPOSITIONS;

        if ($response->getStatus() !== Response::STATUS_SUCCESS) {
            echo $response->getMessage();
        } else {
            $this->dataForTemplate[self::COMPOSITION] = $response->getData();

            try {
                $this->twig->display(
                    'composition/viewComposition.twig',
                    $this->dataForTemplate
                );
            } catch (Throwable $e) {
                echo $e->getMessage();
                die();
            }
        }
    }
};
