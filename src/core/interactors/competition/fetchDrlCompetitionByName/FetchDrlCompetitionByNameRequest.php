<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionByName;


use DrlArchive\core\classes\Request;

class FetchDrlCompetitionByNameRequest extends Request
{
    public const COMPETITION_NAME = 'competitionName';

    protected array $schema = [
        self::COMPETITION_NAME => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
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

}
