<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\event\createDrlEvent;


use DrlArchive\core\classes\Response;

class CreateDrlEventResponse extends Response
{
    public const DATA_DRL_EVENT_ID = 'drlEventId';
    public const DATA_LOCATION_ID = 'locationId';
    public const DATA_COMPETITION_ID = 'competitionId';
    public const DATA_YEAR = 'year';
}
