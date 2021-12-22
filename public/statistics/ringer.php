<?php

declare(strict_types=1);

require_once __DIR__ . '/../init.php';

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\ringerPage\RingerPageRequest;
use DrlArchive\core\interactors\pages\ringerPage\RingerPageResponse;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\pages\RingerPageFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;


$presenter = new class extends AbstractTwigPagePresenter {
    public function send(?Response $response = null): void
    {
        parent::send($response);
        $this->dataForTemplate[self::NAV][self::NAV_HIGHLIGHTED] =
            Implementation::NAV_HIGHLIGHT_STATS;

        if ($response->getStatus() === Response::STATUS_SUCCESS) {
            $this->dataForTemplate[self::RINGER] =
                $response->getData()[RingerPageResponse::DATA_RINGER];
            $this->dataForTemplate[self::EVENTS] =
                $response->getData()[RingerPageResponse::DATA_EVENTS];
            $this->dataForTemplate[self::STATS] =
                $response->getData()[RingerPageResponse::DATA_STATS];
            try {
                $this->twig->display(
                    '/statistics/ringer.twig',
                    $this->dataForTemplate
                );
            } catch (Throwable $e) {
                echo $e->getMessage();
                die();
            }
        } else {
            echo $response->getMessage();
        }
    }
};

$request = new RingerPageRequest();
$request->setRingerId((int)$_GET['ringerId'] ?? 0);

$useCase = (new RingerPageFactory())->create(
    $presenter,
    $request
);
$useCase->execute();
