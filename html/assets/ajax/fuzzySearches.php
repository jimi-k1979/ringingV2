<?php

declare(strict_types=1);

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\interactors\competition\drlCompetitionFuzzySearch\DrlCompetitionFuzzySearchRequest;
use DrlArchive\core\interactors\competition\fetchDrlCompetitionByLocation\FetchDrlCompetitionByLocationRequest;
use DrlArchive\core\interactors\event\FetchDrlEventAndResults\FetchDrlEventAndResultsRequest;
use DrlArchive\core\interactors\event\FetchDrlEventAndResults\FetchDrlEventAndResultsResponse;
use DrlArchive\core\interactors\event\fetchDrlEventsByCompetitionIdAndLocation\FetchDrlEventsByCompetitionIdAndLocationRequest;
use DrlArchive\core\interactors\event\FetchDrlEventsByYear\FetchDrlEventsByYearRequest;
use DrlArchive\core\interactors\event\FetchEventsByCompetition\FetchEventsByCompetitionRequest;
use DrlArchive\core\interactors\location\locationFuzzySearch\LocationFuzzySearchRequest;
use DrlArchive\core\interactors\team\TeamFuzzySearch\TeamFuzzySearchRequest;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use DrlArchive\implementation\factories\interactors\competition\DrlCompetitionFuzzySearchFactory;
use DrlArchive\implementation\factories\interactors\competition\FetchDrlCompetitionByLocationFactory;
use DrlArchive\implementation\factories\interactors\event\FetchDrlEventAndResultsFactory;
use DrlArchive\implementation\factories\interactors\event\FetchDrlEventsByCompetitionIdAndLocationFactory;
use DrlArchive\implementation\factories\interactors\event\FetchDrlEventsByYearFactory;
use DrlArchive\implementation\factories\interactors\event\FetchEventsByCompetitionFactory;
use DrlArchive\implementation\factories\interactors\location\LocationFuzzySearchFactory;
use DrlArchive\implementation\factories\interactors\team\TeamFuzzySearchFactory;
use DrlArchive\implementation\presenters\FuzzySearchPresenterJson;
use DrlArchive\implementation\presenters\ResultsSearchDropdownPresenter;

require_once __DIR__ . '/../../../vendor/autoload.php';

header('Content-Type: application/json');

try {
    switch ($_POST['action']) {
        case 'fuzzySearchCompetitions':
            $request = new DrlCompetitionFuzzySearchRequest(
                [
                    DrlCompetitionFuzzySearchRequest::SEARCH_TERM =>
                        $_POST['term'],
                ]
            );

            $useCase = (new DrlCompetitionFuzzySearchFactory())->create(
                new FuzzySearchPresenterJson(),
                $request
            );
            $useCase->execute();

            break;

        case 'fuzzySearchLocations':
            $request = new LocationFuzzySearchRequest(
                [
                    LocationFuzzySearchRequest::SEARCH_TERM => $_POST['term'],
                ]
            );

            $useCase = (new LocationFuzzySearchFactory())->create(
                new FuzzySearchPresenterJson(),
                $request
            );
            $useCase->execute();

            break;

        case 'fuzzySearchTeams':
            $request = new TeamFuzzySearchRequest();
            $request->setSearchTerm($_POST['term']);

            $useCase = (new TeamFuzzySearchFactory())->create(
                new FuzzySearchPresenterJson(),
                $request
            );
            $useCase->execute();

            break;

        case 'getCompetitionYears':
            $request = new FetchEventsByCompetitionRequest(
                [
                    FetchEventsByCompetitionRequest::COMPETITION =>
                        $_POST['competition'],
                    FetchEventsByCompetitionRequest::COMPETITION_TYPE =>
                        AbstractCompetitionEntity::COMPETITION_TYPE_DRL,
                ]
            );
            $useCase = (new FetchEventsByCompetitionFactory())->create(
                new ResultsSearchDropdownPresenter(),
                $request
            );
            $useCase->execute();

            break;

        case 'getLocationEvents':
            $request = new FetchDrlCompetitionByLocationRequest(
                [
                    FetchDrlCompetitionByLocationRequest::LOCATION_NAME =>
                        $_POST['location'],
                ]
            );
            $useCase = (new FetchDrlCompetitionByLocationFactory())->create(
                new ResultsSearchDropdownPresenter(),
                $request
            );
            $useCase->execute();

            break;

        case 'getLocationEventYears':
            $request = new FetchDrlEventsByCompetitionIdAndLocationRequest();
            $request->setCompetitionId((int)$_POST['competitionId']);
            $request->setLocation($_POST['location']);

            $useCase = (new FetchDrlEventsByCompetitionIdAndLocationFactory())
                ->create(
                    new ResultsSearchDropdownPresenter(),
                    $request
                );
            $useCase->execute();
            break;

        case 'getYearEvents':
            $request = new FetchDrlEventsByYearRequest();
            $request->setYear($_POST['eventYear']);

            $useCase = (new FetchDrlEventsByYearFactory())->create(
                new ResultsSearchDropdownPresenter(),
                $request
            );
            $useCase->execute();
            break;

        case 'getResults':
            $request = new FetchDrlEventAndResultsRequest(
                [
                    FetchDrlEventAndResultsRequest::EVENT_ID =>
                        $_POST['eventId'],
                ]
            );

            $presenter = new class implements PresenterInterface {
                public function send(?Response $response = null): void
                {
                    $data = $response->getData();
                    if ($response->getStatus() === Response::STATUS_SUCCESS) {
                        $responseArray = $data;
                    } else {
                        $responseArray = [
                            'code' => $data[Response::DATA_CODE],
                        ];
                        if (
                            $data[Response::DATA_CODE] ===
                            ResultRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
                        ) {
                            $responseArray['event'] =
                                $data[Response::DATA];
                        }
                    }

                    echo json_encode($responseArray);
                }
            };

            $useCase = (new FetchDrlEventAndResultsFactory())->create(
                $presenter,
                $request
            );
            $useCase->execute();

            break;

        default:
            echo json_encode(
                [
                    [
                        'name' => 'Nothing found',
                        'id' => 0,
                        'text' => 'Not found'
                    ],
                ]
            );
    }
} catch (Exception $e) {
    echo json_encode(
        [
            [
                'name' => 'Nothing found',
                'id' => 0,
                'text' => 'Not found'
            ],
        ]
    );
}
