<?php

namespace DrlArchive\core\interactors\pages\teamPage;

use DrlArchive\core\classes\Response;

class TeamPageResponse extends Response
{
    public const DATA_RESULTS = 'results';

    public const DATA_STATS = 'stats';
    public const DATA_STATS_OPTIONS = 'statsOptions';
    public const DATA_STATS_OPTIONS_START_YEAR = 'startYear';
    public const DATA_STATS_OPTIONS_END_YEAR = 'endYear';
    public const DATA_STATS_RANGE_SUMMARY = 'rangeSummary';
    public const DATA_STATS_SEASONAL = 'seasonal';

    public const DATA_TEAM = 'team';
    public const DATA_TEAM_DEANERY = 'deanery';
    public const DATA_TEAM_ID = 'id';
    public const DATA_TEAM_NAME = 'name';
    public const DATA_TEAM_REGION = 'region';
    public const DATA_TEAM_EARLIEST_YEAR = 'earliestYear';
    public const DATA_TEAM_MOST_RECENT_YEAR = 'mostRecentYear';


}
