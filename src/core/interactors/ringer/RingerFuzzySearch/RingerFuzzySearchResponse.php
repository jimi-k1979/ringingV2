<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\ringer\RingerFuzzySearch;


use DrlArchive\core\classes\Response;

class RingerFuzzySearchResponse extends Response
{
    public const DATA_ID = 'id';
    public const DATA_FIRST_NAME = 'firstName';
    public const DATA_LAST_NAME = 'lastName';
    public const DATA_FULL_NAME = 'fullName';
}
