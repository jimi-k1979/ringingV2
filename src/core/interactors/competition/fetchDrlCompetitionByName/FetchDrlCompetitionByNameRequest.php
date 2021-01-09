<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionByName;


use DrlArchive\core\classes\Request;

class FetchDrlCompetitionByNameRequest extends Request
{
    public const COMPETITION_NAME = 'competitionName';
    public const YEAR = 'year';

    protected array $schema = [
        self::COMPETITION_NAME => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
        self::YEAR => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
    ];

    public function getCompetitionName(): string
    {
        return $this->data[self::COMPETITION_NAME];
    }

    public function setCompetitionName(string $input): void
    {
        $this->updateModel(self::COMPETITION_NAME, $input);
    }

    public function getYear(): int
    {
        return $this->data[self::YEAR];
    }

    public function setYear(int $input): void
    {
        $this->updateModel(self::YEAR, $input);
    }

}
