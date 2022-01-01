<?php

namespace DrlArchive\core\interactors\pages\teamPage;

use DrlArchive\core\classes\Request;
use DrlArchive\core\Constants;

class TeamPageRequest extends Request
{
    public const TEAM_ID = 'teamId';
    public const SHOW_STATS = 'showStats';
    public const SHOW_RESULTS = 'showResults';
    public const STATS_OPTIONS = 'statsOptions';

    public const STATS_START_YEAR = 'startYear';
    public const STATS_END_YEAR = 'endYear';
    public const STATS_RANGE_SUMMARY = 'rangeSummary';
    public const STATS_SEASONAL = 'seasonal';

    public const STATS_EVENT_COUNT = 'eventCount';
    public const STATS_EVENTS_PER_SEASON = 'eventsPerSeason';
    public const STATS_FAULT_DIFFERENCE = 'faultDifference';
    public const STATS_FAULT_DIFFERENCE_MEAN = 'faultDifferenceMean';
    public const STATS_FAULT_DIFFERENCE_MEDIAN = 'faultDifferenceMedian';
    public const STATS_FAULT_DIFFERENCE_RANGE = 'faultDifferenceRange';
    public const STATS_FAULT_DIFFERENCE_TOTAL = 'faultDifferenceTotal';
    public const STATS_FAULT_MEAN = 'faultMean';
    public const STATS_FAULT_MEDIAN = 'faultMedian';
    public const STATS_FAULT_RANGE = 'faultRange';
    public const STATS_FAULT_TOTAL = 'faultTotal';
    public const STATS_FIRST_YEAR = 'firstYear';
    public const STATS_LEAGUE_POINT_MEAN = 'leaguePointMean';
    public const STATS_LEAGUE_POINT_MEDIAN = 'leaguePointMedian';
    public const STATS_LEAGUE_POINT_MODE = 'leaguePointMode';
    public const STATS_LEAGUE_POINT_RANGE = 'leaguePointRange';
    public const STATS_LEAGUE_POINT_TOTAL = 'leaguePointTotal';
    public const STATS_MOST_RECENT_YEAR = 'mostRecentYear';
    public const STATS_NO_RESULT_COUNT = 'noResultCount';
    public const STATS_SEASON_COUNT = 'seasonCount';
    public const STATS_POSITION_MEAN = 'positionMean';
    public const STATS_POSITION_MEDIAN = 'positionMedian';
    public const STATS_POSITION_MODE = 'positionMode';
    public const STATS_POSITION_RANGE = 'positionRange';
    public const STATS_RANKING_MEAN = 'rankingMean';
    public const STATS_RANKING_MEDIAN = 'rankingMedian';
    public const STATS_RANKING_MODE = 'rankingMode';
    public const STATS_RANKING_RANGE = 'rankingRange';

    protected array $schema = [
        self::TEAM_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => 0,
        ],
        self::SHOW_STATS => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_BOOL,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => true,
        ],
        self::SHOW_RESULTS => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_BOOL,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => false,
        ],
        self::STATS_OPTIONS => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_ARRAY,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => [
                self::STATS_START_YEAR => Constants::MINIMUM_YEAR,
                self::STATS_END_YEAR => null,
                self::STATS_RANGE_SUMMARY => [
                    self::STATS_FIRST_YEAR => true,
                    self::STATS_MOST_RECENT_YEAR => true,
                    self::STATS_SEASON_COUNT => false,
                    self::STATS_EVENT_COUNT => true,
                    self::STATS_EVENTS_PER_SEASON => false,
                    self::STATS_RANKING_MEAN => true,
                    self::STATS_RANKING_MEDIAN => false,
                    // 'rankingMode' is not greatly informative
                    self::STATS_RANKING_RANGE => false,
                    self::STATS_POSITION_MEAN => true,
                    self::STATS_POSITION_MEDIAN => false,
                    self::STATS_POSITION_MODE => false,
                    self::STATS_POSITION_RANGE => false,
                    self::STATS_FAULT_TOTAL => true,
                    self::STATS_FAULT_MEAN => true,
                    self::STATS_FAULT_MEDIAN => false,
                    // 'faultMode' is not greatly informative
                    self::STATS_FAULT_RANGE => false,
                    self::STATS_FAULT_DIFFERENCE_TOTAL => true,
                    self::STATS_FAULT_DIFFERENCE_MEAN => false,
                    self::STATS_FAULT_DIFFERENCE_MEDIAN => false,
                    // 'faultDifferenceMode' is not greatly informative
                    self::STATS_FAULT_DIFFERENCE_RANGE => false,
                    self::STATS_LEAGUE_POINT_TOTAL => true,
                    self::STATS_LEAGUE_POINT_MEAN => true,
                    self::STATS_LEAGUE_POINT_MEDIAN => false,
                    // 'leaguePointMode' is not greatly informative
                    self::STATS_LEAGUE_POINT_RANGE => false,
                    self::STATS_NO_RESULT_COUNT => true,
                ],
                self::STATS_SEASONAL => [
                    self::STATS_EVENT_COUNT => true,
                    self::STATS_FAULT_TOTAL => true,
                    self::STATS_FAULT_MEAN => true,
                    self::STATS_FAULT_MEDIAN => false,
                    self::STATS_FAULT_RANGE => false,
                    self::STATS_POSITION_MEAN => true,
                    self::STATS_POSITION_MEDIAN => false,
                    self::STATS_POSITION_MODE => false,
                    self::STATS_POSITION_RANGE => false,
                    self::STATS_NO_RESULT_COUNT => true,
                    self::STATS_LEAGUE_POINT_TOTAL => true,
                    self::STATS_LEAGUE_POINT_MEAN => true, // aka ranking
                    self::STATS_LEAGUE_POINT_MEDIAN => false,
                    self::STATS_LEAGUE_POINT_MODE => false,
                    self::STATS_LEAGUE_POINT_RANGE => false,
                    self::STATS_FAULT_DIFFERENCE => true,
                ],
            ]
        ],
    ];

    public function getTeamId(): int
    {
        return $this->data[self::TEAM_ID];
    }

    public function setTeamId(int $input): void
    {
        $this->updateModel(self::TEAM_ID, $input);
    }

    public function isShowStats(): bool
    {
        return $this->data[self::SHOW_STATS];
    }

    public function setShowStats(bool $input): void
    {
        $this->updateModel(self::SHOW_STATS, $input);
    }

    public function isShowResults(): bool
    {
        return $this->data[self::SHOW_RESULTS];
    }

    public function setShowResults(bool $input): void
    {
        $this->updateModel(self::SHOW_RESULTS, $input);
    }

    public function getStatsOptions(): array
    {
        return $this->data[self::STATS_OPTIONS];
    }

    public function setStatsOptions(array $input): void
    {
        $this->updateModel(self::STATS_OPTIONS, $input);
    }

}
