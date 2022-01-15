<?php

declare(strict_types=1);

require_once __DIR__ . '/../init.php';

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\eventPage\EventPageRequest;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\pages\EventPageFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;


$presenter = new class extends AbstractTwigPagePresenter {
    private const PEAL = 'pealNumber';
    private const PEAL_NUMBERS = 'pealNumbers';
    private const STATISTICS = 'statistics';
    private const MEAN_FAULTS = 'meanFaults';

    public function send(?Response $response = null): void
    {
        parent::send($response);
        $this->dataForTemplate[self::NAV][self::NAV_HIGHLIGHTED] =
            Implementation::NAV_HIGHLIGHT_ARCHIVE;

        if ($response->getStatus() !== Response::STATUS_SUCCESS) {
            echo $response->getMessage();
        } else {
            $this->dataForTemplate[self::EVENT] = $response->getData();
            $this->dataForTemplate[self::EVENT][self::PEAL_NUMBERS] =
                !empty($this->dataForTemplate[self::EVENT][self::RESULTS][0][self::PEAL]);

            $this->dataForTemplate[self::EVENT][self::STATISTICS][self::MEAN_FAULTS] =
                number_format(
                    $this->dataForTemplate[self::EVENT][self::STATISTICS][self::MEAN_FAULTS],
                    2
                );
            try {
                $this->twig->display(
                    'archive/event.twig',
                    $this->dataForTemplate
                );
            } catch (Throwable $e) {
                echo $e->getMessage();
                die();
            }
        }
    }
};

$request = new EventPageRequest();
$request->setEventId((int)$_GET['eventId'] ?? 0);

$useCase = (new EventPageFactory())->create(
    $presenter,
    $request
);
$useCase->execute();


