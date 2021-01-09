<?php

declare(strict_types=1);

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\competition\fetchDrlCompetitionByName\FetchDrlCompetitionByNameRequest;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\implementation\factories\interactors\competition\FetchDrlCompetitionByNameFactory;

require_once __DIR__ . '/../../../vendor/autoload.php';

header('Content-Type: application/json');

try {
    switch ($_POST['action']) {
        case 'getCompetitionDetails':
            $request = new FetchDrlCompetitionByNameRequest();
            $request->setCompetitionName($_POST['competition']);
            $request->setYear((int)$_POST['year']);

            $presenter = new class implements PresenterInterface {

                public function send(?Response $response = null)
                {
                    if ($response->getStatus() === Response::STATUS_SUCCESS) {
                        $jsonArray = [
                            'name' => $response->getData()['name'],
                            'id' => $response->getData()['id'],
                            'isSingleTower' => $response
                                ->getData()['isSingleTowerCompetition'],
                        ];
                        if ($response->getData()['isSingleTowerCompetition']) {
                            $jsonArray['usualLocationName'] = $response
                                ->getData()['usualLocation']['location'];
                            $jsonArray['usualLocationId'] = $response
                                ->getData()['usualLocation']['id'];
                        }
                        echo json_encode($jsonArray);
                    } else {
                        echo json_encode(
                            [
                                'name' => 'Nothing found',
                                'id' => 0,
                                'text' => $response->getMessage(),
                            ]
                        );
                    }
                }
            };

            $useCase = (new FetchDrlCompetitionByNameFactory())->create(
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
