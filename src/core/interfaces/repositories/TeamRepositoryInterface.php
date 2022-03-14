<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\Constants;
use DrlArchive\core\entities\RecordStatisticFieldEntity;
use DrlArchive\core\entities\RecordRequestOptionsEntity;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;

interface TeamRepositoryInterface
{
    public const UNABLE_TO_INSERT_EXCEPTION = 2201;
    public const NO_ROWS_FOUND_EXCEPTION = 2202;
    public const NO_ROWS_UPDATED = 2203;
    public const NO_ROWS_DELETED = 2204;

    /**
     * @param TeamEntity $teamEntity
     * @throws CleanArchitectureException
     */
    public function insertTeam(TeamEntity $teamEntity): void;

    /**
     * @param int $teamId
     * @return TeamEntity
     * @throws CleanArchitectureException
     */
    public function fetchTeamById(int $teamId): TeamEntity;

    /**
     * @param TeamEntity $teamEntity
     * @throws CleanArchitectureException
     */
    public function updateTeam(TeamEntity $teamEntity): void;

    /**
     * @param string $searchTerm
     * @return TeamEntity[]
     * @throws CleanArchitectureException
     */
    public function fuzzySearchTeam(string $searchTerm): array;

    /**
     * @param string $teamName
     * @return TeamEntity
     * @throws CleanArchitectureException
     */
    public function fetchTeamByName(string $teamName): TeamEntity;

    /**
     * @param TeamEntity $team
     * @param int $startYear
     * @param int|null $endYear
     * @return array
     * @throws CleanArchitectureException
     */
    public function fetchTeamStatistics(
        TeamEntity $team,
        int $startYear = Constants::MINIMUM_YEAR,
        ?int $endYear = null
    ): array;

    /**
     * @param TeamEntity $team
     * @param int $startYear
     * @param int|null $endYear
     * @return array
     * @throws CleanArchitectureException
     */
    public function fetchTeamResults(
        TeamEntity $team,
        int $startYear = Constants::MINIMUM_YEAR,
        ?int $endYear = null
    ): array;

    /**
     * @param RecordRequestOptionsEntity $inputData
     * @return RecordStatisticFieldEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchTeamListByNumberOfCompetitions(
        RecordRequestOptionsEntity $inputData
    ): array;

    /**
     * @param RecordRequestOptionsEntity $inputData
     * @return RecordStatisticFieldEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchTeamListByNumberOfWins(
        RecordRequestOptionsEntity $inputData
    ): array;

    /**
     * @param RecordRequestOptionsEntity $inputData
     * @return RecordStatisticFieldEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchTeamListByWinPercentage(
        RecordRequestOptionsEntity $inputData
    ): array;

    /**
     * @param RecordRequestOptionsEntity $inputData
     * @return RecordStatisticFieldEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchTeamListByFaultScore(RecordRequestOptionsEntity $inputData): array;

    /**
     * @param RecordRequestOptionsEntity $inputData
     * @return RecordStatisticFieldEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchTeamListByMeanFaultScore(RecordRequestOptionsEntity $inputData): array;

    /**
     * @param RecordRequestOptionsEntity $inputData
     * @return RecordStatisticFieldEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchTeamListByTotalFaultScore(RecordRequestOptionsEntity $inputData): array;

    /**
     * @param RecordRequestOptionsEntity $inputData
     * @return RecordStatisticFieldEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchTeamListByFaultDifference(RecordRequestOptionsEntity $inputData): array;

    /**
     * @param RecordRequestOptionsEntity $inputData
     * @return RecordStatisticFieldEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchWinningTeamListByFaultScore(RecordRequestOptionsEntity $inputData): array;

    /**
     * @param RecordRequestOptionsEntity $inputData
     * @return RecordStatisticFieldEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchLastPlaceTeamListByFaultScore(RecordRequestOptionsEntity $inputData): array;

}
