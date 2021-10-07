<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\checkDrlEventExists;


use DrlArchive\core\classes\Response;

class CheckDrlEventExistsResponse extends Response
{
    public const DATA_EVENT_ID = 'eventId';
    public const DATA_YEAR = 'year';
    public const DATA_COMPETITION = 'competition';
    public const DATA_LOCATION = 'location';
    public const DATA_COMPETITION_ID = 'competitionId';
    public const DATA_COMPETITION_NAME = 'competitionName';
    public const DATA_SINGLE_TOWER = 'singleTower';
    public const DATA_USUAL_LOCATION = 'usualLocation';
    public const DATA_USUAL_LOCATION_ID = 'usualLocationId';
}
