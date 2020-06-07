<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionById;


use DrlArchive\core\classes\Request;

class FetchDrlCompetitionByIdRequest extends Request
{
    public const COMPETITION_ID = 'competitionId';

    protected $schema = [
        self::COMPETITION_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
    ];

    public function getCompetitionId(): int
    {
        return $this->data[self::COMPETITION_ID];
    }

    public function setCompetitionId(int $input): void
    {
        $this->updateModel(self::COMPETITION_ID, $input);
    }


}