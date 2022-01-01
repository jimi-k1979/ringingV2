<?php

declare(strict_types=1);

require_once __DIR__ . '/../init.php';

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\teamPage\TeamPageRequest;
use DrlArchive\core\interactors\pages\teamPage\TeamPageResponse;
use DrlArchive\core\StatFieldNames;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\pages\TeamPageFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;


$presenter = new class extends AbstractTwigPagePresenter {
    public function send(?Response $response = null): void
    {
        parent::send($response);
        $this->dataForTemplate[self::NAV][self::NAV_HIGHLIGHTED] =
            Implementation::NAV_HIGHLIGHT_STATS;

        if ($response->getStatus() === Response::STATUS_SUCCESS) {
            $this->dataForTemplate[self::TEAM] =
                $response->getData()[TeamPageResponse::DATA_TEAM];
            $this->dataForTemplate[self::STATS] =
                $response->getData()[TeamPageResponse::DATA_STATS];
            $this->dataForTemplate[self::STATS_OPTIONS] =
                $response->getData()[TeamPageResponse::DATA_STATS_OPTIONS];
            try {
                $this->twig->display(
                    'statistics/team.twig',
                    $this->dataForTemplate
                );
            } catch (Throwable $e) {
                echo $response->getMessage();
                die();
            }
        } else {
            echo $response->getMessage();
        }
    }
};

$request = new TeamPageRequest();
$request->setTeamId((int)$_GET['id']);
$request->setShowStats(true);
$request->setShowResults(true);

if (isset($_GET['statsOptions'])) {
    // decode and apply
}
if (isset($_GET['startYear'])) {
    $request->getStatsOptions()[StatFieldNames::STATS_START_YEAR] =
        (int)$_GET['startYear'];
}
if (isset($_GET['endYear'])) {
    $request->getStatsOptions()[StatFieldNames::STATS_END_YEAR] =
        (int)$_GET['endYear'];
}

$useCase = (new TeamPageFactory())->create(
    $presenter,
    $request
);
$useCase->execute();
