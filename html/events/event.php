<?php

declare(strict_types=1);

require_once __DIR__ . '/../init.php';

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\eventPage\EventPageRequest;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\pages\EventPageFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;


$presenter = new class extends AbstractTwigPagePresenter {
    public function send(?Response $response = null): void
    {
        parent::send($response);
        $this->dataForTemplate['nav']['highlighted'] =
            Implementation::NAV_HIGHLIGHT_ARCHIVE;

        if ($response->getStatus() !== Response::STATUS_SUCCESS) {
        } else {
            $this->dataForTemplate['event'] = $response->getData();
            $this->dataForTemplate['event']['pealNumbers'] =
                !empty($this->dataForTemplate['event']['results'][0]['pealNumber']);

            try {
                $this->twig->display(
                    'events/event.twig',
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


