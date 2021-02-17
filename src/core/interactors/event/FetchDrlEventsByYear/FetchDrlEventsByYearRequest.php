<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchDrlEventsByYear;


use DrlArchive\core\classes\Request;

class FetchDrlEventsByYearRequest extends Request
{
    public const YEAR = 'year';

    protected array $schema = [
        self::YEAR => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
    ];

    public function getYear(): string
    {
        return $this->data[self::YEAR];
    }

    public function setYear(string $input): void
    {
        $this->updateModel(self::YEAR, $input);
    }


}