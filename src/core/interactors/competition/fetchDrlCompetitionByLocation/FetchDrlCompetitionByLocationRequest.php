<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionByLocation;


use DrlArchive\core\classes\Request;

class FetchDrlCompetitionByLocationRequest extends Request
{
    public const LOCATION_NAME = 'locationId';

    protected array $schema = [
        self::LOCATION_NAME => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
    ];

    public function getLocationName(): string
    {
        return $this->data[self::LOCATION_NAME];
    }

    public function setLocationName(string $input): void
    {
        $this->updateModel(self::LOCATION_NAME, $input);
    }

}
