<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\location\fetchLocationByName;


use DrlArchive\core\classes\Request;

class FetchLocationByNameRequest extends Request
{
    public const NAME = 'name';

    protected array $schema = [
        self::NAME => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
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


}
