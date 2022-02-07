<?php

namespace DrlArchive\core\interactors\pages\recordsPage;

use DrlArchive\core\classes\Response;

class RecordsPageResponse extends Response
{
    public const EVENTS_HIGHEST_ENTRY = 'highestEntry';
    public const EVENTS_HIGHEST_TOTAL_FAULTS = 'highestTotalFaults';
    public const EVENTS_LOWEST_TOTAL_FAULTS = 'lowestTotalFaults';
    public const EVENTS_HIGHEST_MEAN_FAULTS = 'highestMeanFaults';
    public const EVENTS_LOWEST_MEAN_FAULTS = 'lowestMeanFaults';
    public const EVENTS_LARGEST_VICTORY_MARGIN = 'largestVictoryMargin';
    public const EVENTS_SMALLEST_VICTORY_MARGIN = 'smallestVictoryMargin';
}
