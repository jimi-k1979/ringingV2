<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchDrlEventAndResults;


use DrlArchive\core\classes\Response;

class FetchDrlEventAndResultsResponse extends Response
{
    public const DATA_EVENT = 'event';
    public const DATA_EVENT_YEAR = 'year';
    public const DATA_EVENT_COMPETITION = 'competition';
    public const DATA_EVENT_SINGLE_TOWER = 'singleTower';
    public const DATA_EVENT_LOCATION = 'location';
    public const DATA_EVENT_UNUSUAL_TOWER = 'unusualTower';
    public const DATA_EVENT_ID = 'eventId';
    public const DATA_RESULTS = 'results';
    public const DATA_RESULTS_POSITION = 'position';
    public const DATA_RESULTS_PEAL_NUMBER = 'pealNumber';
    public const DATA_RESULTS_TEAM = 'team';
    public const DATA_RESULTS_FAULTS = 'faults';
}
