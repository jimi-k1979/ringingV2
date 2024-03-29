<?php

declare(strict_types=1);

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\event\checkDrlEventExists\CheckDrlEventExistsRequest;
use DrlArchive\core\interactors\event\checkDrlEventExists\CheckDrlEventExistsResponse;
use DrlArchive\core\interactors\location\fetchLocationByName\FetchLocationByNameRequest;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\implementation\factories\interactors\event\CheckDrlEventExistsFactory;
use DrlArchive\implementation\factories\interactors\location\FetchLocationByNameFactory;

require_once __DIR__ . '/../../../vendor/autoload.php';

header('Content-Type: application/json');

try {
    switch ($_POST['action']) {
        case 'getCompetitionDetails':
            try {
                $request = new CheckDrlEventExistsRequest();
                $request->setCompetitionName($_POST['competition']);
                $request->setEventYear($_POST['year']);

                $presenter = new class implements PresenterInterface {

                    public function send(?Response $response = null): void
                    {
                        if ($response->getStatus() === Response::STATUS_SUCCESS) {
                            $data = $response->getData();
                            if (
                                isset($data[CheckDrlEventExistsResponse::DATA_EVENT_ID])
                            ) {
                                echo json_encode(
                                    [
                                        'message' => 'Event already exists in database',
                                        'status' => 400,
                                    ]
                                );
                            } else {
                                $competition = [
                                    'status' => 200,
                                    'competitionId' =>
                                        $data[CheckDrlEventExistsResponse::DATA_COMPETITION_ID],
                                ];
                                if ($data['singleTower']) {
                                    $competition = array_merge(
                                        $competition,
                                        [
                                            'usualLocationId' =>
                                                $data[CheckDrlEventExistsResponse::DATA_USUAL_LOCATION_ID],
                                            'usualLocation' =>
                                                $data[CheckDrlEventExistsResponse::DATA_USUAL_LOCATION],
                                        ]
                                    );
                                }
                                echo json_encode(
                                    $competition
                                );
                            }
                        } else {
                            echo json_encode(
                                [
                                    'message' => $response->getMessage(),
                                    'status' => 500,
                                ]
                            );
                        }
                    }
                };

                $useCase = (new CheckDrlEventExistsFactory())->create(
                    $presenter,
                    $request
                );

                $useCase->execute();
            } catch (Throwable $e) {
                echo json_encode(
                    [
                        'status' => 500,
                        'message' => 'unknown error',
                    ]
                );
            }
            break;

        case 'getLocationId':
            try {
                $request = new FetchLocationByNameRequest();
                $request->setName($_POST['location']);

                $presenter = new class implements PresenterInterface {

                    public function send(?Response $response = null): void
                    {
                        if ($response->getStatus() === Response::STATUS_SUCCESS) {
                            $data = $response->getData();

                            $location = [
                                'status' => 200,
                                'locationId' => $data['locationId'],
                            ];

                            echo json_encode(
                                $location
                            );
                        } else {
                            echo json_encode(
                                [
                                    'message' => $response->getMessage(),
                                    'status' => 500,
                                ]
                            );
                        }
                    }
                };

                $useCase = (new FetchLocationByNameFactory())->create(
                    $presenter,
                    $request
                );
                $useCase->execute();
            } catch (Throwable $e) {
                echo json_encode(
                    [
                        'status' => 500,
                        'message' => 'unknown error',
                    ]
                );
            }
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
            break;
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
