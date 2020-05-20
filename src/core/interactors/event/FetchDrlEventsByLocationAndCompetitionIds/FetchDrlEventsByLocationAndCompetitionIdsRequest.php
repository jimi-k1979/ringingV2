<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchDrlEventsByLocationAndCompetitionIds;


use DrlArchive\core\classes\Request;

class FetchDrlEventsByLocationAndCompetitionIdsRequest extends Request
{
    public const LOCATION_ID = 'locationId';
    public const COMPETITION_ID = 'competitionId';

    protected $schema = [
        self::LOCATION_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
        self::COMPETITION_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
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


}