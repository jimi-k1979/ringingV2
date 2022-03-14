<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\Constants;
use DrlArchive\core\entities\RecordRequestOptionsEntity;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockTeamTrait;

class TeamDummy implements TeamRepositoryInterface
{
    use CreateMockTeamTrait;

    /**
     * @inheritDoc
     */
    public function insertTeam(TeamEntity $teamEntity): void
    {
        $teamEntity->setId(TestConstants::TEST_TEAM_ID);
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamById(int $teamId): TeamEntity
    {
        return $this->createMockTeam();
    }

    /**
     * @inheritDoc
     */
    public function updateTeam(TeamEntity $teamEntity): void
    {
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchTeam(string $searchTerm): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamByName(string $teamName): TeamEntity
    {
        return $this->createMockTeam();
    }

    /**
     * @param int|null $endYear
     * @param int $startYear
     * @inheritDoc
     */
    public function fetchTeamStatistics(
        TeamEntity $team,
        int $startYear = Constants::MINIMUM_YEAR,
        ?int $endYear = null
    ): array {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamResults(
        TeamEntity $team,
        int $startYear = Constants::MINIMUM_YEAR,
        ?int $endYear = null
    ): array {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByNumberOfCompetitions(RecordRequestOptionsEntity $inputData): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByNumberOfWins(RecordRequestOptionsEntity $inputData): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByWinPercentage(RecordRequestOptionsEntity $inputData): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByFaultScore(RecordRequestOptionsEntity $inputData): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByMeanFaultScore(RecordRequestOptionsEntity $inputData): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByTotalFaultScore(RecordRequestOptionsEntity $inputData): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByFaultDifference(RecordRequestOptionsEntity $inputData): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchWinningTeamListByFaultScore(RecordRequestOptionsEntity $inputData): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchLastPlaceTeamListByFaultScore(RecordRequestOptionsEntity $inputData): array
    {
        return [];
    }
}
