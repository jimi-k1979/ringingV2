<?php

declare(strict_types=1);

use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\interactors\competition\drlCompetitionFuzzySearch\DrlCompetitionFuzzySearchRequest;
use DrlArchive\core\interactors\event\FetchEventsByCompetition\FetchEventsByCompetitionRequest;
use DrlArchive\implementation\factories\interactors\competition\DrlCompetitionFuzzySearchFactory;
use DrlArchive\implementation\factories\interactors\event\FetchEventsByCompetitionFactory;
use DrlArchive\implementation\presenters\FuzzySearchPresenterJson;
use DrlArchive\implementation\presenters\ResultsSearchDropdownPresenter;

require_once __DIR__ . '/../../../vendor/autoload.php';

header('Content-Type: application/json');


switch ($_POST['action']) {
    case 'fuzzySearchCompetitions':
        try {
            $request = new DrlCompetitionFuzzySearchRequest(
                [
                    DrlCompetitionFuzzySearchRequest::SEARCH_TERM => $_POST['term'],
                ]
            );

            $useCase = (new DrlCompetitionFuzzySearchFactory())->create(
                new FuzzySearchPresenterJson(),
                $request
            );
            $useCase->execute();
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

        break;

    case 'fuzzySearchLocations':
        echo json_encode(
            [
                [
                    'name' => 'exeter',
                    'id' => 5,
                ],
                [
                    'name' => 'great torrington',
                    'id' => 6,
                ],
            ]
        );
        break;

    case 'getCompetitionYears':
        try {
            $request = new FetchEventsByCompetitionRequest(
                [
                    FetchEventsByCompetitionRequest::COMPETITION_ID =>
                        $_POST['competitionId'],
                    FetchEventsByCompetitionRequest::COMPETITION_TYPE =>
                        AbstractCompetitionEntity::COMPETITION_TYPE_DRL,
                ]
            );
            $useCase = (new FetchEventsByCompetitionFactory())->create(
                new ResultsSearchDropdownPresenter(),
                $request
            );
            $useCase->execute();
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
        break;

    case 'getLocationEvents':
        echo json_encode(
            [
                [
                    'text' => 'major final',
                    'id' => 1,
                ],
                [
                    'text' => 'minor final',
                    'id' => 2,
                ]
            ]
        );
        break;

    case 'getLocationEventYears':
        echo json_encode(
            [
                [
                    'text' => '1979',
                    'id' => 1,
                ],
                [
                    'text' => '1980',
                    'id' => 2,
                ],
            ]
        );
        break;

    case 'getYearEvents':
        echo json_encode(
            [
                [
                    'text' => '8 bell',
                    'id' => 1,
                ],
                [
                    'text' => 'south devon 8',
                    'id' => 2,
                ],
            ]
        );
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