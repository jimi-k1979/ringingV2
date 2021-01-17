<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


class Repository
{
    public const REPOSITORY_ERROR_ALREADY_EXISTS = 100;
    public const REPOSITORY_ERROR_CONNECTION = 101;
    public const REPOSITORY_ERROR_ACCESS_DENIED = 102;
    public const REPOSITORY_ERROR_WRITE = 103;
    public const REPOSITORY_ERROR_READ = 104;
    public const REPOSITORY_ERROR_NOT_FOUND = 105;
    public const REPOSITORY_ERROR_UNKNOWN = -9999;
    public const METHOD_NOT_IMPLEMENTED_EXCEPTION = 1099;

    public const ALIAS_COMPETITION_ID = 'competitionId';
    public const ALIAS_COMPETITION_NAME = 'competitionName';
    public const ALIAS_IS_SINGLE_TOWER = 'isSingleTower';
    public const ALIAS_USUAL_LOCATION_ID = 'usualLocationId';
    public const ALIAS_USUAL_LOCATION_NAME = 'usualLocation';
    public const ALIAS_DEDICATION = 'dedication';
    public const ALIAS_TENOR_WEIGHT = 'tenorWeight';
    public const ALIAS_NUMBER_OF_BELLS = 'numberOfBells';
    public const ALIAS_DEANERY_ID = 'deaneryId';
    public const ALIAS_DEANERY_NAME = 'deaneryName';
    public const ALIAS_DEANERY_REGION = 'deaneryRegion';
    public const ALIAS_EVENT_ID = 'eventId';
    public const ALIAS_YEAR = 'year';
    public const ALIAS_IS_UNUSUAL_TOWER = 'isUnusualTower';
    public const ALIAS_LOCATION_ID = 'locationId';
    public const ALIAS_LOCATION_NAME = 'locationName';
    public const ALIAS_TEAM_ID = 'teamId';
    public const ALIAS_TEAM_NAME = 'teamName';

}
