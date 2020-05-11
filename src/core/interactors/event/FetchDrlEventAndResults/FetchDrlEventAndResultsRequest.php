<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchDrlEventAndResults;


use DrlArchive\core\classes\Request;

class FetchDrlEventAndResultsRequest extends Request
{
    public const EVENT_ID = 'eventId';

    protected $schema = [
        self::EVENT_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
    ];

    public function getEventId(): int
    {
        return $this->data[self::EVENT_ID];
    }

    public function setEventId(int $input): void
    {
        $this->updateModel(self::EVENT_ID, $input);
    }


}