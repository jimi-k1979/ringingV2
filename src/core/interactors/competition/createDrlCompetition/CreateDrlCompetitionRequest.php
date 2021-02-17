<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\createDrlCompetition;


use DrlArchive\core\classes\Request;

class CreateDrlCompetitionRequest extends Request
{
    public const COMPETITION_NAME = 'name';
    public const IS_SINGLE_TOWER = 'singleTower';

    protected array $schema = [
        self::COMPETITION_NAME => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
        ],
        self::IS_SINGLE_TOWER => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_BOOL,
            parent::OPTION_REQUIRED => false,
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

    public function getSingleTower(): bool
    {
        return $this->data[self::IS_SINGLE_TOWER];
    }

    public function setSingleTower(bool $input): void
    {
        $this->updateModel(self::IS_SINGLE_TOWER, $input);
    }

}