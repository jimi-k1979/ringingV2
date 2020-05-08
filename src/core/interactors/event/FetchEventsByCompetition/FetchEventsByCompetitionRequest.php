<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchEventsByCompetition;


use DrlArchive\core\classes\Request;

class FetchEventsByCompetitionRequest extends Request
{
    public const COMPETITION_ID = 'competitionId';
    public const COMPETITION_TYPE = 'competitionType';

    protected $schema = [
        self::COMPETITION_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => true,
        ],
        self::COMPETITION_TYPE => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => true,
        ]
    ];

    public function getCompetitionId(): int
    {
        return $this->data[self::COMPETITION_ID];
    }

    public function setCompetitionId(int $input): void
    {
        $this->updateModel(self::COMPETITION_ID, $input);
    }

    public function getCompetitionType(): int
    {
        return $this->data[self::COMPETITION_TYPE];
    }

    public function setCompetitionType(int $input): void
    {
        $this->updateModel(self::COMPETITION_TYPE, $input);
    }


}