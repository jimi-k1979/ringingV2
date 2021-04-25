<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\fetchDrlEventsByCompetitionIdAndLocation;


use DrlArchive\core\classes\Request;

class FetchDrlEventsByCompetitionIdAndLocationRequest extends Request
{
    public const LOCATION = 'location';
    public const COMPETITION_ID = 'competitionId';

    protected array $schema = [
        self::LOCATION => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
        self::COMPETITION_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
    ];

    public function getLocation(): string
    {
        return $this->data[self::LOCATION];
    }

    public function setLocation(string $input): void
    {
        $this->updateModel(self::LOCATION, $input);
    }

    public function getCompetitionId(): int
    {
        return $this->data[self::COMPETITION_ID];
    }

    public function setCompetitionId(int $input): void
    {
        $this->updateModel(self::COMPETITION_ID, $input);
    }

}
