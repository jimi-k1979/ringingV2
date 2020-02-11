<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\team\CreateTeam;


use DrlArchive\core\classes\Request;

class CreateTeamRequest extends Request
{
    public const NAME = 'name';
    public const DEANERY = 'deanery';

    protected $schema = [
        self::NAME => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
        ],
        self::DEANERY => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
        ],
    ];

    public function getName(): string
    {
        return $this->data[self::NAME];
    }

    public function setName(string $input): void
    {
        $this->updateModel(self::NAME, $input);
    }

    public function getDeanery(): string
    {
        return $this->data[self::DEANERY];
    }

    public function setDeanery(string $input): void
    {
        $this->updateModel(self::DEANERY, $input);
    }

}