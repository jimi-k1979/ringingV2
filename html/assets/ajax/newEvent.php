<?php

declare(strict_types=1);

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\event\checkDrlEventExists\CheckDrlEventExistsRequest;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\implementation\factories\interactors\event\CheckDrlEventExistsFactory;

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
                    private array $data;

                    public function getData(): array
                    {
                        return $this->data;
                    }

                    public function send(?Response $response = null)
                    {
                        if ($response->getStatus() === Response::STATUS_SUCCESS) {
                            $data = $response->getData();
                            if (isset($data['eventId'])) {
                                echo json_encode(
                                    [
                                        'message' => 'Event already exists in database',
                                        'status' => 400,
                                    ]
                                );
                            } else {
                                $competition = [
                                    'status' => 200,
                                    'competitionId' => $data['competitionId'],
                                ];
                                if ($data['singleTower']) {
                                    $competition = array_merge(
                                        $competition,
                                        [
                                            'usualLocationId' => $data['usualLocationId'],
                                            'usualLocation' => $data['usualLocation'],
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
                // todo - add logged in user ID
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
