<?php

declare(strict_types=1);

namespace DrlArchive\implementation\presenters;


use DrlArchive\core\classes\Response;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;

class FuzzySearchPresenterJson implements PresenterInterface
{
    public const PRESENTER_NAME = 'name';
    public const PRESENTER_ID = 'id';
    public const PRESENTER_TEXT = 'text';

    public function send(?Response $response = null): void
    {
        if ($response->getStatus() !== Response::STATUS_SUCCESS) {
            echo json_encode(
                [
                    self::PRESENTER_NAME => 'Nothing found',
                    self::PRESENTER_ID => 0,
                    self::PRESENTER_TEXT => 'Not found',
                ]
            );
        } else {
            $data = $response->getData();

            $responseArray = [];
            foreach ($data as $datum) {
                $responseArray[] = [
                    self::PRESENTER_ID => $datum['id'],
                    self::PRESENTER_NAME => $datum['name']
                ];
            }

            echo json_encode($responseArray);
        }
    }
}
