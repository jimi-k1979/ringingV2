<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\checkDrlEventExists;


use DrlArchive\core\classes\Request;

class CheckDrlEventExistsRequest extends Request
{
    public const EVENT_YEAR = 'eventYear';
    public const COMPETITION_NAME = 'competitionName';

    protected $schema = [
        self::EVENT_YEAR => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
        self::COMPETITION_NAME => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
    ];

    public function getEventYear(): string
    {
        return $this->data[self::EVENT_YEAR];
    }

    public function setEventYear(string $input): void
    {
        $this->updateModel(self::EVENT_YEAR, $input);
    }

    public function getCompetitionName(): string
    {
        return $this->data[self::COMPETITION_NAME];
    }

    public function setCompetitionName(string $input): void
    {
        $this->updateModel(self::COMPETITION_NAME, $input);
    }


}