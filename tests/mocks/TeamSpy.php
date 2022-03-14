<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\Constants;
use DrlArchive\core\entities\RecordStatisticFieldEntity;
use DrlArchive\core\entities\RecordRequestOptionsEntity;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockTeamTrait;

class TeamSpy implements TeamRepositoryInterface
{
    use CreateMockTeamTrait;

    private int $insertTeamIdValue = TestConstants::TEST_TEAM_ID;
    private bool $insertCalled = false;
    private TeamEntity $selectTeamValue;
    private bool $selectCalled = false;
    private TeamEntity $updateTeamValue;
    private bool $updateCalled = false;
    private bool $deleteTeamValue = false;
    private bool $deletedCalled = false;
    /**
     * @var TeamEntity[]
     */
    private array $fuzzySearchValue;
    private bool $fuzzySearchCalled = false;
    private bool $fuzzySearchThrowsException = false;
    private bool $fetchTeamByNameCalled = false;
    private int $fetchTeamByNameCallCount = 0;
    private bool $fetchTeamByNameThrowsException = false;
    private TeamEntity $fetchTeamByNameValue;
    private bool $fetchTeamStatisticsCalled = false;
    private bool $fetchTeamStatisticsThrowsException = false;
    private array $fetchTeamStatisticsValue = [];
    private bool $fetchTeamResultsCalled = false;
    private bool $fetchTeamResultsThrowsException = false;
    private array $fetchTeamResultsValue = [];
    private bool $fetchTeamListByNumberOfCompetitionsCalled = false;
    private int $fetchTeamListByNumberOfCompetitionsCallCount = 0;
    private ?CleanArchitectureException $fetchTeamListByNumberOfCompetitionsException = null;
    /**
     * @var RecordStatisticFieldEntity[]
     */
    private array $fetchTeamListByNumberOfCompetitionsValue = [];
    private bool $fetchTeamListByNumberOfWinsCalled = false;
    private ?CleanArchitectureException $fetchTeamListByNumberOfWinsException = null;
    /**
     * @var TeamEntity[]
     */
    private array $fetchTeamListByNumberOfWinsValue = [];
    private bool $fetchTeamListByWinPercentageCalled = false;
    private ?CleanArchitectureException $fetchTeamListByWinPercentageException = null;
    /**
     * @var TeamEntity[]
     */
    private array $fetchTeamListByWinPercentageValue = [];
    private bool $fetchTeamListByFaultScoreCalled = true;
    private ?CleanArchitectureException $fetchTeamListByFaultScoreException = null;
    /**
     * @var TeamEntity[]
     */
    private array $fetchTeamListByFaultScoreValue = [];
    private bool $fetchTeamListByMeanFaultScoreCalled = false;
    private ?CleanArchitectureException $fetchTeamListByMeanFaultScoreException = null;
    /**
     * @var TeamEntity[]
     */
    private array $fetchTeamListByMeanFaultScoreValue = [];
    private bool $fetchTeamListByTotalFaultScoreCalled = false;
    private ?CleanArchitectureException $fetchTeamListByTotalFaultScoreException = null;
    /**
     * @var TeamEntity[]
     */
    private array $fetchTeamListByTotalFaultScoreValue = [];
    private bool $fetchTeamListByFaultDifferenceCalled = false;
    private ?CleanArchitectureException $fetchTeamListByFaultDifferenceException = null;
    /**
     * @var TeamEntity[]
     */
    private array $fetchTeamListByFaultDifferenceValue = [];
    private bool $fetchWinningTeamListByFaultScoreCalled = false;
    private ?CleanArchitectureException $fetchWinningTeamListByFaultScoreException = null;
    /**
     * @var TeamEntity[]
     */
    private array $fetchWinningTeamListByFaultScoreValue = [];
    private bool $fetchLastPlaceTeamListByFaultScoreCalled = false;
    private ?CleanArchitectureException $fetchLastPlaceTeamListByFaultScoreException = null;
    /**
     * @var TeamEntity[]
     */
    private array $fetchLastPlaceTeamListByFaultScoreValue = [];


    /**
     * @param TeamEntity $teamEntity
     * @return void
     */
    public function insertTeam(TeamEntity $teamEntity): void
    {
        $this->insertCalled = true;

        $teamEntity->setId($this->insertTeamIdValue);
    }

    /**
     * @param int $id
     */
    public function setInsertTeamIdValue(int $id): void
    {
        $this->insertTeamIdValue = $id;
    }

    /**
     * @return bool
     */
    public function hasInsertTeamBeenCalled(): bool
    {
        return $this->insertCalled;
    }

    /**
     * @param int $teamId
     * @return TeamEntity
     */
    public function fetchTeamById(int $teamId): TeamEntity
    {
        $this->selectCalled = true;

        return $this->selectTeamValue ?? $this->createMockTeam();
    }

    /**
     * @param TeamEntity $teamEntity
     */
    public function setFetchTeamByIdValue(TeamEntity $teamEntity): void
    {
        $this->selectTeamValue = $teamEntity;
    }

    /**
     * @return bool
     */
    public function hasFetchTeamByIdBeenCalled(): bool
    {
        return $this->selectCalled;
    }

    /**
     * @param TeamEntity $teamEntity
     * @return void
     */
    public function updateTeam(TeamEntity $teamEntity): void
    {
        $this->updateCalled = true;

        if (isset($this->updateTeamValue)) {
            $teamEntity = $this->updateTeamValue;
        }
    }

    /**
     * @param TeamEntity $teamEntity
     */
    public function setUpdateTeamValue(TeamEntity $teamEntity): void
    {
        $this->updateTeamValue = $teamEntity;
    }

    /**
     * @return bool
     */
    public function hasUpdateTeamBeenCalled(): bool
    {
        return $this->updateCalled = true;
    }

    /**
     * @param TeamEntity $teamEntity
     * @return bool
     */
    public function deleteTeam(TeamEntity $teamEntity): bool
    {
        $this->deletedCalled = true;

        return $this->deleteTeamValue;
    }

    public function setDeleteTeamValue(): void
    {
        $this->deleteTeamValue = true;
    }

    /**
     * @return bool
     */
    public function hasDeleteTeamBeenCalled(): bool
    {
        return $this->deletedCalled;
    }

    /**
     * @inheritDoc
     * @throws RepositoryNoResultsException
     */
    public function fuzzySearchTeam(string $searchTerm): array
    {
        $this->fuzzySearchCalled = true;

        if ($this->fuzzySearchThrowsException) {
            throw new RepositoryNoResultsException(
                'No teams found',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fuzzySearchValue ?? [$this->createMockTeam()];
    }

    public function setFuzzySearchValue(array $results): void
    {
        $this->fuzzySearchValue = $results;
    }

    public function hasFuzzySearchTeamBeenCalled(): bool
    {
        return $this->fuzzySearchCalled;
    }

    public function setFuzzySearchThrowsException(): void
    {
        $this->fuzzySearchThrowsException = true;
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamByName(string $teamName): TeamEntity
    {
        $this->fetchTeamByNameCalled = true;
        $this->fetchTeamByNameCallCount++;
        if ($this->fetchTeamByNameThrowsException) {
            throw new RepositoryNoResultsException(
                'Team not found',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchTeamByNameValue ?? $this->createMockTeam();
    }

    public function hasFetchTeamByNameBeenCalled(): bool
    {
        return $this->fetchTeamByNameCalled;
    }

    public function getFetchTeamByNameCallCount(): int
    {
        return $this->fetchTeamByNameCallCount;
    }

    public function setFetchTeamByNameThrowsException(): void
    {
        $this->fetchTeamByNameThrowsException = true;
    }

    public function setFetchTeamByNameValue(TeamEntity $value): void
    {
        $this->fetchTeamByNameValue = $value;
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
        $this->fetchTeamStatisticsCalled = true;

        if ($this->fetchTeamStatisticsThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchTeamStatisticsValue;
    }

    public function hasFetchTeamStatisticsBeenCalled(): bool
    {
        return $this->fetchTeamStatisticsCalled;
    }

    public function setFetchTeamStatisticsThrowsException(): void
    {
        $this->fetchTeamStatisticsThrowsException = true;
    }

    public function setFetchTeamStatisticsValue(array $value): void
    {
        $this->fetchTeamStatisticsValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamResults(
        TeamEntity $team,
        int $startYear = Constants::MINIMUM_YEAR,
        ?int $endYear = null
    ): array {
        $this->fetchTeamResultsCalled = true;

        if ($this->fetchTeamResultsThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchTeamResultsValue;
    }

    public function hasFetchTeamResultsBeenCalled(): bool
    {
        return $this->fetchTeamResultsCalled;
    }

    public function setFetchTeamResultsThrowsException(): void
    {
        $this->fetchTeamResultsThrowsException = true;
    }

    public function setFetchTeamResultsValue(array $value): void
    {
        $this->fetchTeamResultsValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByNumberOfCompetitions(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchTeamListByNumberOfCompetitionsCalled = true;
        $this->fetchTeamListByNumberOfCompetitionsCallCount++;
        if ($this->fetchTeamListByNumberOfCompetitionsException) {
            throw $this->fetchTeamListByNumberOfCompetitionsException;
        }

        return $this->fetchTeamListByNumberOfCompetitionsValue;
    }

    public function hasFetchTeamListByNumberOfCompetitionsBeenCalled(): bool
    {
        return $this->fetchTeamListByNumberOfCompetitionsCalled;
    }

    public function getFetchTeamListByNumberOfCompetitionsCallCount(): int
    {
        return $this->fetchTeamListByNumberOfCompetitionsCallCount;
    }

    public function setFetchTeamListByNumberOfCompetitionsException(
        CleanArchitectureException $exception
    ): void {
        $this->fetchTeamListByNumberOfCompetitionsException = $exception;
    }

    public function setFetchTeamListByNumberOfCompetitionsValue(
        array $value
    ): void {
        $this->fetchTeamListByNumberOfCompetitionsValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByNumberOfWins(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchTeamListByNumberOfWinsCalled = true;
        if ($this->fetchTeamListByNumberOfWinsException) {
            throw $this->fetchTeamListByNumberOfWinsException;
        }

        return $this->fetchTeamListByNumberOfWinsValue;
    }

    public function hasFetchTeamListByNumberOfWinsBeenCalled(): bool
    {
        return $this->fetchTeamListByNumberOfWinsCalled;
    }

    public function setFetchTeamListByNumberOfWinsException(
        CleanArchitectureException $exception
    ): void {
        $this->fetchTeamListByNumberOfWinsException = $exception;
    }

    /**
     *
     * @param TeamEntity[] $value
     * @return void
     */
    public function setFetchTeamListByNumberOfWinsValue(array $value): void
    {
        $this->fetchTeamListByNumberOfWinsValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByWinPercentage(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchTeamListByWinPercentageCalled = true;
        if ($this->fetchTeamListByWinPercentageException) {
            throw $this->fetchTeamListByWinPercentageException;
        }
        return $this->fetchTeamListByWinPercentageValue;
    }

    public function hasFetchTeamListByWinPercentageBeenCalled(): bool
    {
        return $this->fetchTeamListByWinPercentageCalled;
    }

    public function setFetchTeamListByWinPercentageException(
        ?CleanArchitectureException $exception
    ): void {
        $this->fetchTeamListByWinPercentageException = $exception;
    }

    /**
     * @param TeamEntity[] $value
     * @return void
     */
    public function setFetchTeamListByWinPercentageValue(array $value): void
    {
        $this->fetchTeamListByWinPercentageValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByFaultScore(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchTeamListByFaultScoreCalled = true;
        if ($this->fetchTeamListByFaultScoreException) {
            throw $this->fetchTeamListByFaultScoreException;
        }
        return $this->fetchTeamListByFaultScoreValue;
    }

    public function hasFetchTeamListByFaultScoreBeenCalled(): bool
    {
        return $this->fetchTeamListByFaultScoreCalled;
    }

    public function setFetchTeamListByFaultScoreException(
        ?CleanArchitectureException $exception
    ): void {
        $this->fetchTeamListByFaultScoreException = $exception;
    }

    /**
     * @param TeamEntity[] $list
     */
    public function setFetchTeamListByFaultScoreValue(array $list): void
    {
        $this->fetchTeamListByFaultScoreValue = $list;
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByMeanFaultScore(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchTeamListByMeanFaultScoreCalled = true;
        if ($this->fetchTeamListByMeanFaultScoreException) {
            throw $this->fetchTeamListByMeanFaultScoreException;
        }
        return $this->fetchTeamListByMeanFaultScoreValue;
    }

    public function hasFetchTeamListByMeanFaultScoreBeenCalled(): bool
    {
        return $this->fetchTeamListByMeanFaultScoreCalled;
    }

    /**
     * @param CleanArchitectureException|null $exception
     */
    public function setFetchTeamListByMeanFaultScoreException(
        ?CleanArchitectureException $exception
    ): void {
        $this->fetchTeamListByMeanFaultScoreException = $exception;
    }

    /**
     * @param TeamEntity[] $value
     */
    public function setFetchTeamListByMeanFaultScoreValue(array $value): void
    {
        $this->fetchTeamListByMeanFaultScoreValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByTotalFaultScore(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchTeamListByTotalFaultScoreCalled = true;
        if ($this->fetchTeamListByTotalFaultScoreException) {
            throw $this->fetchTeamListByTotalFaultScoreException;
        }
        return $this->fetchTeamListByTotalFaultScoreValue;
    }

    public function hasFetchTeamListByTotalFaultScoreBeenCalled(): bool
    {
        return $this->fetchTeamListByTotalFaultScoreCalled;
    }

    /**
     * @param CleanArchitectureException|null $exception
     */
    public function setFetchTeamListByTotalFaultScoreException(
        ?CleanArchitectureException $exception
    ): void {
        $this->fetchTeamListByTotalFaultScoreException = $exception;
    }

    /**
     * @param TeamEntity[] $value
     */
    public function setFetchTeamListByTotalFaultScoreValue(array $value): void
    {
        $this->fetchTeamListByTotalFaultScoreValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamListByFaultDifference(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchTeamListByFaultDifferenceCalled = true;
        if ($this->fetchTeamListByFaultDifferenceException) {
            throw $this->fetchTeamListByFaultDifferenceException;
        }
        return $this->fetchTeamListByFaultDifferenceValue;
    }

    /**
     * @return bool
     */
    public function hasFetchTeamListByFaultDifferenceBeenCalled(): bool
    {
        return $this->fetchTeamListByFaultDifferenceCalled;
    }

    /**
     * @param CleanArchitectureException|null $exception
     */
    public function setFetchTeamListByFaultDifferenceException(
        ?CleanArchitectureException $exception
    ): void {
        $this->fetchTeamListByFaultDifferenceException = $exception;
    }

    /**
     * @param TeamEntity[] $value
     */
    public function setFetchTeamListByFaultDifferenceValue(array $value): void
    {
        $this->fetchTeamListByFaultDifferenceValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchWinningTeamListByFaultScore(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchWinningTeamListByFaultScoreCalled = true;
        if ($this->fetchWinningTeamListByFaultScoreException) {
            throw $this->fetchWinningTeamListByFaultScoreException;
        }
        return $this->fetchWinningTeamListByFaultScoreValue;
    }

    /**
     * @return bool
     */
    public function hasFetchWinningTeamListByFaultScoreBeenCalled(): bool
    {
        return $this->fetchWinningTeamListByFaultScoreCalled;
    }

    /**
     * @param CleanArchitectureException|null $exception
     */
    public function setFetchWinningTeamListByFaultScoreException(
        ?CleanArchitectureException $exception
    ): void {
        $this->fetchWinningTeamListByFaultScoreException = $exception;
    }

    /**
     * @param TeamEntity[] $value
     */
    public function setFetchWinningTeamListByFaultScoreValue(array $value): void
    {
        $this->fetchWinningTeamListByFaultScoreValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchLastPlaceTeamListByFaultScore(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchLastPlaceTeamListByFaultScoreCalled = true;
        if ($this->fetchLastPlaceTeamListByFaultScoreException) {
            throw $this->fetchWinningTeamListByFaultScoreException;
        }
        return $this->fetchLastPlaceTeamListByFaultScoreValue;
    }
}
