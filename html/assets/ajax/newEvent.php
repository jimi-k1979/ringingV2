<?php

declare(strict_types=1);

use DrlArchive\core\classes\Response;
use DrlArchive\core\Exceptions\BadDataException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\competition\fetchDrlCompetitionByName\FetchDrlCompetitionByNameRequest;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\implementation\factories\interactors\competition\FetchDrlCompetitionByNameFactory;

require_once __DIR__ . '/../../../vendor/autoload.php';

header('Content-Type: application/json');

try {
    switch ($_POST['action']) {
        case 'getCompetitionDetails':
            try {
                $request = new FetchDrlCompetitionByNameRequest();
                $request->setCompetitionName($_POST['competition']);
                $request->setYear((int)$_POST['year']);

                $presenter = new class implements PresenterInterface {
                    private array $data;

                    public function getData(): array
                    {
                        return $this->data;
                    }

                    public function send(?Response $response = null)
                    {
                        if ($response->getStatus() === Response::STATUS_SUCCESS) {
                            $this->data = $response->getData();
                        } else {
                            throw new BadDataException(
                                $response->getMessage(),
                                $response->getStatus()
                            );
                        }
                    }
                };

                $useCase = (new FetchDrlCompetitionByNameFactory())->create(
                    $presenter,
                    $request
                );
                $useCase->execute();

                $data = $presenter->getData();

                if ($data['isSingleTowerCompetition']) {
                    $locationData = [
                        'usualLocationId' => $data['usualLocation']['id'],
                        'usualLocationName' => $data['usualLocation']['location'],
                    ];
                } else {
                    $locationData = [];
                }
                echo json_encode(
                    array_merge(
                        [
                            'id' => $data['id'],
                            'name' => $data['name'],
                            'isSingleTower' => $data['isSingleTowerCompetition'],
                        ],
                        $locationData
                    )
                );
            } catch (CleanArchitectureException $e) {
                echo json_encode(
                    [
                        'name' => 'Not found',
                        'id' => 0,
                        'text' => $e->getMessage()
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
