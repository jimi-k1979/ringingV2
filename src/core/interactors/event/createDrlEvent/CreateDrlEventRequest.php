<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\event\createDrlEvent;


use DrlArchive\core\classes\Request;

class CreateDrlEventRequest extends Request
{
    public const LOCATION_ID = 'locationId';
    public const COMPETITION_ID = 'competitionId';
    public const YEAR = 'year';

    protected $schema = [
        self::LOCATION_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
        ],
        self::COMPETITION_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
        ],
        self::YEAR => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
        ]
    ];

    public function getLocationId(): int
    {
        return $this->data[self::LOCATION_ID];
    }

    public function setLocationId(int $input): void
    {
        $this->updateModel(self::LOCATION_ID, $input);
    }

    public function getCompetitionId(): int
    {
        return $this->data[self::COMPETITION_ID];
    }

    public function setCompetitionId(int $input): void
    {
        $this->updateModel(self::COMPETITION_ID, $input);
    }

    public function getYear(): string
    {
        return $this->data[self::YEAR];
    }

    public function setYear(string $input): void
    {
        $this->updateModel(self::YEAR, $input);
    }

}