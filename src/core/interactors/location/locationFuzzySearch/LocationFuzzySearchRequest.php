<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\location\locationFuzzySearch;


use DrlArchive\core\classes\Request;

class LocationFuzzySearchRequest extends Request
{
    public const SEARCH_TERM = 'searchTerm';

    protected $schema = [
        self::SEARCH_TERM => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
        ]
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