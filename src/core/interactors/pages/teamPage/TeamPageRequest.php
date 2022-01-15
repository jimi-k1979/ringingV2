<?php

namespace DrlArchive\core\interactors\pages\teamPage;

use DrlArchive\core\classes\Request;
use DrlArchive\core\Constants;
use DrlArchive\core\StatFieldNames;

class TeamPageRequest extends Request
{
    public const TEAM_ID = 'teamId';
    public const END_YEAR = 'endYear';
    public const SHOW_RESULTS = 'showResults';
    public const SHOW_STATS = 'showStats';
    public const START_YEAR = 'startYear';
    public const STATS_OPTIONS = 'statsOptions';

    protected array $schema = [
        self::TEAM_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => 0,
        ],
        self::END_YEAR => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => true,
            parent::OPTION_DEFAULT => null,
        ],
        self::SHOW_RESULTS => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_BOOL,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => false,
        ],
        self::SHOW_STATS => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_BOOL,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => true,
        ],
        self::START_YEAR => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => Constants::MINIMUM_YEAR,
        ],
        self::STATS_OPTIONS => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_ARRAY,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => [
                StatFieldNames::RANGE_SUMMARY => [
                    StatFieldNames::FIRST_YEAR => true,
                    StatFieldNames::MOST_RECENT_YEAR => true,
                    StatFieldNames::SEASON_COUNT => false,
                    StatFieldNames::EVENT_COUNT => true,
                    StatFieldNames::EVENTS_PER_SEASON => false,
                    StatFieldNames::RANKING_MEAN => true,
                    StatFieldNames::RANKING_MEDIAN => false,
                    // 'rankingMode' is not greatly informative
                    StatFieldNames::RANKING_RANGE => false,
                    StatFieldNames::POSITION_MEAN => true,
                    StatFieldNames::POSITION_MEDIAN => false,
                    StatFieldNames::POSITION_MODE => false,
                    StatFieldNames::POSITION_RANGE => false,
                    StatFieldNames::FAULT_TOTAL => true,
                    StatFieldNames::FAULT_MEAN => true,
                    StatFieldNames::FAULT_MEDIAN => false,
                    // 'faultMode' is not greatly informative
                    StatFieldNames::FAULT_RANGE => false,
                    StatFieldNames::FAULT_DIFFERENCE_TOTAL => true,
                    StatFieldNames::FAULT_DIFFERENCE_MEAN => false,
                    StatFieldNames::FAULT_DIFFERENCE_MEDIAN => false,
                    // 'faultDifferenceMode' is not greatly informative
                    StatFieldNames::FAULT_DIFFERENCE_RANGE => false,
                    StatFieldNames::LEAGUE_POINT_TOTAL => true,
                    StatFieldNames::LEAGUE_POINT_MEAN => true,
                    StatFieldNames::LEAGUE_POINT_MEDIAN => false,
                    // 'leaguePointMode' is not greatly informative
                    StatFieldNames::LEAGUE_POINT_RANGE => false,
                    StatFieldNames::NO_RESULT_COUNT => true,
                ],
                StatFieldNames::SEASONAL => [
                    StatFieldNames::EVENT_COUNT => true,
                    StatFieldNames::FAULT_TOTAL => true,
                    StatFieldNames::FAULT_MEAN => true,
                    StatFieldNames::FAULT_MEDIAN => false,
                    StatFieldNames::FAULT_RANGE => false,
                    StatFieldNames::POSITION_MEAN => true,
                    StatFieldNames::POSITION_MEDIAN => false,
                    StatFieldNames::POSITION_MODE => false,
                    StatFieldNames::POSITION_RANGE => false,
                    StatFieldNames::NO_RESULT_COUNT => true,
                    StatFieldNames::LEAGUE_POINT_TOTAL => true,
                    StatFieldNames::LEAGUE_POINT_MEAN => true, // aka ranking
                    StatFieldNames::LEAGUE_POINT_MEDIAN => false,
                    StatFieldNames::LEAGUE_POINT_MODE => false,
                    StatFieldNames::LEAGUE_POINT_RANGE => false,
                    StatFieldNames::FAULT_DIFFERENCE => true,
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

    public function getStartYear(): int
    {
        return $this->data[self::START_YEAR];
    }

    public function setStartYear(int $input): void
    {
        $this->updateModel(self::START_YEAR, $input);
    }

    public function getEndYear(): ?int
    {
        return $this->data[self::END_YEAR];
    }

    public function setEndYear(?int $input): void
    {
        $this->updateModel(self::END_YEAR, $input);
    }

}
