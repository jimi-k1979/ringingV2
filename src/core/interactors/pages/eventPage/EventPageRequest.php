<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\eventPage;


use DrlArchive\core\classes\Request;

class EventPageRequest extends Request
{
    public const EVENT_ID = 'requestId';

    protected array $schema = [
        self::EVENT_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => 0,
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
