<?php

declare(strict_types=1);
require_once __DIR__ . '/../../../vendor/autoload.php';

use DrlArchive\core\classes\Response;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;

header('Content-Type: application/json');

$presenter = new class implements PresenterInterface {

    public function send(?Response $response = null)
    {
        // TODO: Implement send() method.
    }
};

switch ($_POST['action']) {
    case 'fuzzySearchCompetitions':
        echo json_encode(
            [
                [
                    'name' => 'Value',
                    'id' => 123,
                ],
                [
                    'name' => 'another value',
                    'id' => 124,
                ],
            ]
        );
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
        echo json_encode(
            [
                [
                    'id' => 1,
                    'text' => '1980',
                ],
                [
                    'id' => 2,
                    'text' => '1982',
                ],
            ]
        );
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