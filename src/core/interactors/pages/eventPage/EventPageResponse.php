<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\eventPage;


use DrlArchive\core\classes\Response;

class EventPageResponse extends Response
{
    public const DATA_EVENT_ID = 'eventId';
    public const DATA_EVENT_YEAR = 'eventYear';
    public const DATA_EVENT_LOCATION = 'eventLocation';
    public const DATA_IS_UNUSUAL_LOCATION = 'isUnusualLocation';
    public const DATA_COMPETITION_NAME = 'competitionName';
    public const DATA_RESULTS = 'results';
    public const DATA_RESULTS_POSITION = 'position';
    public const DATA_RESULTS_FAULTS = 'faults';
    public const DATA_RESULTS_TEAM = 'team';
    public const DATA_RESULTS_PEAL_NUMBER = 'pealNumber';
    public const DATA_RESULTS_TEAM_ID = 'teamId';
    public const DATA_JUDGES = 'judges';
    public const DATA_JUDGES_ID = 'id';
    public const DATA_JUDGES_NAME = 'name';
    public const DATA_WINNING_TEAM = 'winningTeam';
    public const DATA_WINNING_TEAM_ID = 'id';
    public const DATA_WINNING_TEAM_NAME = 'name';
    public const DATA_WINNING_TEAM_BELL = 'bell';
    public const DATA_STATS = 'statistics';
    public const DATA_STATS_TOTAL_FAULTS = 'totalFaults';
    public const DATA_STATS_MEAN_FAULTS = 'meanFaults';
    public const DATA_STATS_WINNING_MARGIN = 'winningMargin';
    
}
