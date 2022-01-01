<?php

namespace DrlArchive\core\interactors\pages\teamPage;

use DrlArchive\core\classes\Request;
use DrlArchive\core\Constants;
use DrlArchive\core\StatFieldNames;

class TeamPageRequest extends Request
{
    public const TEAM_ID = 'teamId';
    public const SHOW_STATS = 'showStats';
    public const SHOW_RESULTS = 'showResults';
    public const STATS_OPTIONS = 'statsOptions';

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
                StatFieldNames::STATS_START_YEAR => Constants::MINIMUM_YEAR,
                StatFieldNames::STATS_END_YEAR => null,
                StatFieldNames::STATS_RANGE_SUMMARY => [
                    StatFieldNames::STATS_FIRST_YEAR => true,
                    StatFieldNames::STATS_MOST_RECENT_YEAR => true,
                    StatFieldNames::STATS_SEASON_COUNT => false,
                    StatFieldNames::STATS_EVENT_COUNT => true,
                    StatFieldNames::STATS_EVENTS_PER_SEASON => false,
                    StatFieldNames::STATS_RANKING_MEAN => true,
                    StatFieldNames::STATS_RANKING_MEDIAN => false,
                    // 'rankingMode' is not greatly informative
                    StatFieldNames::STATS_RANKING_RANGE => false,
                    StatFieldNames::STATS_POSITION_MEAN => true,
                    StatFieldNames::STATS_POSITION_MEDIAN => false,
                    StatFieldNames::STATS_POSITION_MODE => false,
                    StatFieldNames::STATS_POSITION_RANGE => false,
                    StatFieldNames::STATS_FAULT_TOTAL => true,
                    StatFieldNames::STATS_FAULT_MEAN => true,
                    StatFieldNames::STATS_FAULT_MEDIAN => false,
                    // 'faultMode' is not greatly informative
                    StatFieldNames::STATS_FAULT_RANGE => false,
                    StatFieldNames::STATS_FAULT_DIFFERENCE_TOTAL => true,
                    StatFieldNames::STATS_FAULT_DIFFERENCE_MEAN => false,
                    StatFieldNames::STATS_FAULT_DIFFERENCE_MEDIAN => false,
                    // 'faultDifferenceMode' is not greatly informative
                    StatFieldNames::STATS_FAULT_DIFFERENCE_RANGE => false,
                    StatFieldNames::STATS_LEAGUE_POINT_TOTAL => true,
                    StatFieldNames::STATS_LEAGUE_POINT_MEAN => true,
                    StatFieldNames::STATS_LEAGUE_POINT_MEDIAN => false,
                    // 'leaguePointMode' is not greatly informative
                    StatFieldNames::STATS_LEAGUE_POINT_RANGE => false,
                    StatFieldNames::STATS_NO_RESULT_COUNT => true,
                ],
                StatFieldNames::STATS_SEASONAL => [
                    StatFieldNames::STATS_EVENT_COUNT => true,
                    StatFieldNames::STATS_FAULT_TOTAL => true,
                    StatFieldNames::STATS_FAULT_MEAN => true,
                    StatFieldNames::STATS_FAULT_MEDIAN => false,
                    StatFieldNames::STATS_FAULT_RANGE => false,
                    StatFieldNames::STATS_POSITION_MEAN => true,
                    StatFieldNames::STATS_POSITION_MEDIAN => false,
                    StatFieldNames::STATS_POSITION_MODE => false,
                    StatFieldNames::STATS_POSITION_RANGE => false,
                    StatFieldNames::STATS_NO_RESULT_COUNT => true,
                    StatFieldNames::STATS_LEAGUE_POINT_TOTAL => true,
                    StatFieldNames::STATS_LEAGUE_POINT_MEAN => true, // aka ranking
                    StatFieldNames::STATS_LEAGUE_POINT_MEDIAN => false,
                    StatFieldNames::STATS_LEAGUE_POINT_MODE => false,
                    StatFieldNames::STATS_LEAGUE_POINT_RANGE => false,
                    StatFieldNames::STATS_FAULT_DIFFERENCE => true,
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
