<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionByLocation;


use DrlArchive\core\classes\Request;

class FetchDrlCompetitionByLocationRequest extends Request
{
    public const LOCATION_ID = 'locationId';

    protected array $schema = [
        self::LOCATION_ID => [
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

}
