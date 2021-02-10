<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\readDrlEvent;


use DrlArchive\core\classes\Request;

class ReadDrlEventRequest extends Request
{
    public const DRL_EVENT_ID = 'eventId';

    protected array $schema = [
        self::DRL_EVENT_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_REQUIRED => true,
        ],
    ];

    public function getEventId(): int
    {
        return $this->data[self::DRL_EVENT_ID];
    }

    public function setEventId(int $input): void
    {
        $this->updateModel(self::DRL_EVENT_ID, $input);
    }


}
