<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\ringer\RingerFuzzySearch;


use DrlArchive\core\classes\Request;

class RingerFuzzySearchRequest extends Request
{
    public const SEARCH_TERM = 'searchTerm';

    protected $schema = [
        self::SEARCH_TERM => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
    ];

    public function getSearchTerm(): string
    {
        return $this->data[self::SEARCH_TERM];
    }

    public function setSearchTerm(string $input): void
    {
        $this->updateModel(self::SEARCH_TERM, $input);
    }

}