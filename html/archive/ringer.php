<?php

declare(strict_types=1);

require_once __DIR__ . '/../init.php';

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\ringerPage\RingerPageRequest;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\pages\RingerPageFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;


$presenter = new class extends AbstractTwigPagePresenter {
    public function send(?Response $response = null): void
    {
        parent::send($response);
        $this->dataForTemplate['nav']['highlighted'] =
            Implementation::NAV_HIGHLIGHT_ARCHIVE;

        if ($response->getStatus() === Response::STATUS_SUCCESS) {
            $this->dataForTemplate['ringer'] = $response->getData()['ringer'];
            $this->dataForTemplate['events'] = $response->getData()['events'];
            try {
                $this->twig->display(
                    'archive/ringer.twig',
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
