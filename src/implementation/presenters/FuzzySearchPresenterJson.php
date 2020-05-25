<?php

declare(strict_types=1);

namespace DrlArchive\implementation\presenters;


use DrlArchive\core\classes\Response;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;

class FuzzySearchPresenterJson implements PresenterInterface
{

    public function send(?Response $response = null)
    {
        if ($response->getStatus() !== Response::STATUS_SUCCESS) {
            echo json_encode(
                [
                    'name' => 'Nothing found',
                    'id' => 0,
                    'text' => 'Not found',
                ]
            );
        } else {
            $data = $response->getData();

            $responseArray = [];
            foreach ($data as $datum) {
                $responseArray[] = [
                    'id' => $datum['id'],
                    'name' => $datum['name']
                ];
            }

            echo json_encode($responseArray);
        }
    }
}