<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\location\createLocation;


use DrlArchive\core\classes\Request;

class CreateLocationRequest extends Request
{
    public const LOCATION_NAME = 'location';
    public const DEANERY = 'deanery';
    public const DEDICATION = 'dedication';
    public const TENOR_WEIGHT = 'tenorWeight';

    protected $schema = [
        self::LOCATION_NAME => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
        ],
        self::DEANERY => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
        ],
        self::DEDICATION => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
        ],
        self::TENOR_WEIGHT => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
        ],
    ];

    public function getLocation(): string
    {
        return $this->data[self::LOCATION_NAME];
    }

    public function setLocation(string $input): void
    {
        $this->updateModel(self::LOCATION_NAME, $input);
    }

    public function getDeanery(): int
    {
        return $this->data[self::DEANERY];
    }

    public function setDeanery(int $input): void
    {
        $this->updateModel(self::DEANERY, $input);
    }

    public function getDedication(): string
    {
        return $this->data[self::DEDICATION];
    }

    public function setDedication(string $input): void
    {
        $this->updateModel(self::DEDICATION, $input);
    }

    public function getTenorWeight(): string
    {
        return $this->data[self::TENOR_WEIGHT];
    }

    public function setTenorWeight(string $input): void
    {
        $this->updateModel(self::TENOR_WEIGHT, $input);
    }

}