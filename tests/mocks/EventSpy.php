<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\entities\RecordRequestOptionsEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockDrlEventTrait;

class EventSpy implements EventRepositoryInterface
{
    use CreateMockDrlEventTrait;

    private bool $insertEventCalled = false;
    private int $insertDrlEventIdValue = TestConstants::TEST_EVENT_ID;
    private bool $insertDrlEventThrowsException = false;
    private bool $fetchDrlEventCalled = false;
    private bool $fetchDrlEventThrowsException = false;
    private DrlEventEntity $fetchDrlEventValue;
    private bool $fetchDrlEventsByCompetitionIdCalled = false;
    private bool $fetchDrlEventsByCompetitionIdThrowsException = false;
    /**
     * @var DrlEventEntity[]
     */
    private array $fetchDrlEventsByCompetitionIdValue = [];
    private bool $fetchDrlEventsByCompetitionAndLocationIdsCalled = false;
    private bool $fetchDrlEventsByCompetitionAndLocationIdsThrowsException = false;
    /**
     * @var DrlEventEntity[]
     */
    private array $fetchDrlEventsByCompetitionAndLocationIdsValue = [];
    private bool $fetchDrlEventsByYearCalled = false;
    private bool $fetchDrlEventsByYearThrowsException = false;
    /**
     * @var DrlEventEntity[]
     */
    private array $fetchDrlEventsByYearValue = [];
    private bool $fetchDrlEventByYearAndCompetitionNameCalled = false;
    private bool $fetchDrlEventByYearAndCompetitionNameThrowsException = false;
    private bool $fetchDrlEventByYearAndCompetitionIdCalled = false;
    private bool $fetchDrlEventByYearAndCompetitionIdThrowsException = false;
    private DrlEventEntity $fetchDrlEventByYearAndCompetitionIdValue;

    private DrlEventEntity $fetchDrlEventByYearAndCompetitionNameValue;

    private bool $fetchDrlEventsByCompetitionNameCalled = false;
    private bool $fetchDrlEventsByCompetitionNameThrowsException = false;
    private array $fetchDrlEventsByCompetitionNameValue = [];
    private bool $fetchDrlEventsByCompetitionIdAndVenueCalled = false;
    private bool $fetchDrlEventsByCompetitionIdAndVenueThrowsException = false;
    private array $fetchDrlEventsByCompetitionIdAndVenueValue = [];
    private bool $fetchSingleDrlEventStatisticsCalled = false;
    private bool $fetchSingleDrlEventStatisticsThrowsException = false;
    /**
     * @var float[]
     */
    private array $eventStats;
    private bool $fetchJudgeDrlEventListCalled = false;
    private bool $fetchJudgeDrlEventListThrowsException = false;
    /**
     * @var DrlEventEntity[]
     */
    private array $fetchJudgeDrlEventListValue = [];
    private bool $fetchDrlEventListByEntryCalled = false;
    private int $fetchDrlEventListByEntryCallCount = 0;
    private ?CleanArchitectureException $fetchDrlEventListByEntryException = null;
    /**
     * @var DrlEventEntity[]
     */
    private array $fetchDrlEventListByEntryValue = [];
    private bool $fetchDrlEventListByTotalFaultsCalled = false;
    private int $fetchDrlEventListByTotalFaultsCallCount = 0;
    private ?CleanArchitectureException $fetchDrlEventListByTotalFaultsException = null;
    /**
     * @var DrlEventEntity[]
     */
    private array $fetchDrlEventListByTotalFaultsValue = [];
    private bool $fetchDrlEventListByMeanFaultsCalled = false;
    private int $fetchDrlEventListByMeanFaultsCallCount = 0;
    private ?CleanArchitectureException $fetchDrlEventListByMeanFaultsException = null;
    /**
     * @var DrlEventEntity[]
     */
    private array $fetchDrlEventListByMeanFaultsValue = [];
    private bool $fetchDrlEventListByVictoryMarginCalled;
    private int $fetchDrlEventListByVictoryMarginCallCount = 0;
    private ?CleanArchitectureException $fetchDrlEventListByVictoryMarginException = null;
    /**
     * @var DrlEventEntity[]
     */
    private array $fetchDrlEventListByVictoryMarginValue = [];


    /**
     * @param DrlEventEntity $entity
     * @return void
     * @throws GeneralRepositoryErrorException
     */
    public function insertDrlEvent(DrlEventEntity $entity): void
    {
        $this->insertEventCalled = true;
        if ($this->insertDrlEventThrowsException) {
            throw new GeneralRepositoryErrorException(
                "Can't insert event",
                EventRepositoryInterface::NO_ROWS_CREATED_EXCEPTION
            );
        }
        $entity->setId($this->insertDrlEventIdValue);
    }

    /**
     * @return bool
     */
    public function hasInsertEventBeenCalled(): bool
    {
        return $this->insertEventCalled;
    }

    /**
     * @param int $insertDrlEventIdValue
     */
    public function setInsertDrlEventIdValue(int $insertDrlEventIdValue): void
    {
        $this->insertDrlEventIdValue = $insertDrlEventIdValue;
    }
    
    public function setInsertDrlEventThrowsException(): void
    {
        $this->insertDrlEventThrowsException = true;
    }

    /**
     * @param int $id
     * @return DrlEventEntity
     * @throws RepositoryNoResultsException
     */
    public function fetchDrlEvent(int $id): DrlEventEntity
    {
        $this->fetchDrlEventCalled = true;
        if ($this->fetchDrlEventThrowsException) {
            throw new RepositoryNoResultsException(
                'No drl event found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchDrlEventValue ?? $this->createMockDrlEvent();
    }

    /**
     */
    public function setFetchDrlEventThrowsException(): void
    {
        $this->fetchDrlEventThrowsException = true;
    }

    /**
     * @param DrlEventEntity $fetchDrlEventValue
     */
    public function setFetchDrlEventValue(
        DrlEventEntity $fetchDrlEventValue
    ): void {
        $this->fetchDrlEventValue = $fetchDrlEventValue;
    }

    /**
     * @return bool
     */
    public function hasFetchDrlEventBeenCalled(): bool
    {
        return $this->fetchDrlEventCalled;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByCompetitionId(int $competitionId): array
    {
        $this->fetchDrlEventsByCompetitionIdCalled = true;
        if ($this->fetchDrlEventsByCompetitionIdThrowsException) {
            throw new RepositoryNoResultsException(
                'No events found for that competition id',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }
        return $this->fetchDrlEventsByCompetitionIdValue;
    }

    public function hasFetchDrlEventsByCompetitionIdBeenCalled(): bool
    {
        return $this->fetchDrlEventsByCompetitionIdCalled;
    }

    public function setFetchDrlEventsByCompetitionIdValue(array $value): void
    {
        $this->fetchDrlEventsByCompetitionIdValue = $value;
    }

    public function setFetchDrlEventsByCompetitionIdThrowsException(): void
    {
        $this->fetchDrlEventsByCompetitionIdThrowsException = true;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByCompetitionAndLocationIds(int $competitionId, int $locationId): array
    {
        $this->fetchDrlEventsByCompetitionAndLocationIdsCalled = true;
        if ($this->fetchDrlEventsByCompetitionAndLocationIdsThrowsException) {
            throw new RepositoryNoResultsException(
                'No events found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchDrlEventsByCompetitionAndLocationIdsValue;
    }

    public function hasFetchDrlEventsByCompetitionAndLocationIdsBeenCalled(): bool
    {
        return $this->fetchDrlEventsByCompetitionAndLocationIdsCalled;
    }

    public function setFetchDrlEventsByCompetitionAndLocationIdsThrowsException(): void
    {
        $this->fetchDrlEventsByCompetitionAndLocationIdsThrowsException = true;
    }

    public function setFetchDrlEventsByCompetitionAndLocationIdsValue(array $value): void
    {
        $this->fetchDrlEventsByCompetitionAndLocationIdsValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByYear(string $year): array
    {
        $this->fetchDrlEventsByYearCalled = true;
        if ($this->fetchDrlEventsByYearThrowsException) {
            throw new RepositoryNoResultsException(
                'No events found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchDrlEventsByYearValue;
    }

    public function hasFetchDrlEventsByYearBeenCalled(): bool
    {
        return $this->fetchDrlEventsByYearCalled;
    }

    public function setFetchDrlEventsByYearThrowsException(): void
    {
        $this->fetchDrlEventsByYearThrowsException = true;
    }

    public function setFetchDrlEventsByYearValue(array $value): void
    {
        $this->fetchDrlEventsByYearValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventByYearAndCompetitionName(
        string $year,
        string $competitionName
    ): DrlEventEntity {
        $this->fetchDrlEventByYearAndCompetitionNameCalled = true;
        if ($this->fetchDrlEventByYearAndCompetitionNameThrowsException) {
            throw new RepositoryNoResultsException(
                'No event found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchDrlEventByYearAndCompetitionNameValue ??
            $this->createMockDrlEvent();
    }

    public function hasFetchDrlEventByYearAndCompetitionNameBeenCalled(): bool
    {
        return $this->fetchDrlEventByYearAndCompetitionNameCalled;
    }

    public function setFetchDrlEventByYearAndCompetitionNameThrowsException(): void
    {
        $this->fetchDrlEventByYearAndCompetitionNameThrowsException = true;
    }

    public function setFetchDrlEventByYearAndCompetitionNameValue(
        DrlEventEntity $entity
    ): void {
        $this->fetchDrlEventByYearAndCompetitionNameValue = $entity;
    }

    public function fetchDrlEventByYearAndCompetitionId(
        string $year,
        int $competitionId
    ): DrlEventEntity {
        $this->fetchDrlEventByYearAndCompetitionIdCalled = true;
        if ($this->fetchDrlEventByYearAndCompetitionIdThrowsException) {
            throw new RepositoryNoResultsException(
                'No event found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchDrlEventByYearAndCompetitionIdValue ??
            $this->createMockDrlEvent();
    }

    public function hasFetchDrlEventByYearAndCompetitionIdBeenCalled(): bool
    {
        return $this->fetchDrlEventByYearAndCompetitionIdCalled;
    }

    public function setFetchDrlEventByYearAndCompetitionIdThrowsException(): void
    {
        $this->fetchDrlEventByYearAndCompetitionIdThrowsException = true;
    }

    public function setFetchDrlEventByYearAndCompetitionIdValue(
        DrlEventEntity $value
    ): void {
        $this->fetchDrlEventByYearAndCompetitionIdValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByCompetitionName(string $name): array
    {
        $this->fetchDrlEventsByCompetitionNameCalled = true;
        if ($this->fetchDrlEventsByCompetitionNameThrowsException) {
            throw new CleanArchitectureException(
                'something went wrong',
                EventRepositoryInterface::INVALID_EVENT_TYPE_EXCEPTION
            );
        }

        return $this->fetchDrlEventsByCompetitionNameValue;
    }

    public function hasFetchDrlEventsByCompetitionNameBeenCalled(): bool
    {
        return $this->fetchDrlEventsByCompetitionNameCalled;
    }

    public function setFetchDrlEventsByCompetitionNameThrowsException(): void
    {
        $this->fetchDrlEventsByCompetitionNameThrowsException = true;
    }

    public function setFetchDrlEventsByCompetitionNameValue(array $value): void
    {
        $this->fetchDrlEventsByCompetitionNameValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByCompetitionIdAndVenue(
        int $competitionId,
        string $locationName
    ): array {
        $this->fetchDrlEventsByCompetitionIdAndVenueCalled = true;
        if ($this->fetchDrlEventsByCompetitionIdAndVenueThrowsException) {
            throw new RepositoryNoResultsException(
                'No events found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchDrlEventsByCompetitionIdAndVenueValue;
    }

    public function hasFetchDrlEventsByCompetitionIdAndVenueBeenCalled(): bool
    {
        return $this->fetchDrlEventsByCompetitionIdAndVenueCalled;
    }

    public function setFetchDrlEventsByCompetitionIdAndVenueThrowsException(): void
    {
        $this->fetchDrlEventsByCompetitionIdAndVenueThrowsException = true;
    }

    public function setFetchDrlEventsByCompetitionIdAndVenueValue(array $value): void
    {
        $this->fetchDrlEventsByCompetitionIdAndVenueValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchSingleDrlEventStatistics(DrlEventEntity $event): void
    {
        $this->fetchSingleDrlEventStatisticsCalled = true;
        if ($this->fetchDrlEventsByCompetitionNameThrowsException) {
            throw new CleanArchitectureException('something went wrong');
        }
        $event->setTotalFaults(
            $this->eventStats['totalFaults'] ?? TestConstants::TEST_EVENT_TOTAL_FAULTS
        );
        $event->setMeanFaults(
            $this->eventStats['meanFaults'] ?? TestConstants::TEST_EVENT_MEAN_FAULTS
        );
        $event->setWinningMargin(
            $this->eventStats['winningMargin'] ?? TestConstants::TEST_EVENT_WINNING_MARGIN
        );
    }

    public function hasFetchSingleDrlEventStatisticsBeenCalled(): bool
    {
        return $this->fetchSingleDrlEventStatisticsCalled;
    }

    public function setFetchSingleDrlEventStatisticsThrowsException(): void
    {
        $this->fetchSingleDrlEventStatisticsThrowsException = true;
    }

    public function setFetchSingleDrlEventStatisticsValues(float $total, float $mean, float $margin): void
    {
        $this->eventStats = [
            'totalFaults' => $total,
            'meanFaults' => $mean,
            'winningMargin' => $margin,
        ];
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventListByJudge(JudgeEntity $judge): array
    {
        $this->fetchJudgeDrlEventListCalled = true;
        if ($this->fetchJudgeDrlEventListThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }
        return $this->fetchJudgeDrlEventListValue
            ?? [$this->createMockDrlEvent()];
    }

    public function hasFetchJudgeDrlEventListBeenCalled(): bool
    {
        return $this->fetchJudgeDrlEventListCalled;
    }

    public function setFetchJudgeDrlEventListThrowsException(): void
    {
        $this->fetchJudgeDrlEventListThrowsException = true;
    }

    public function setFetchJudgeDrlEventListValue(array $value): void
    {
        $this->fetchJudgeDrlEventListValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventListByEntry(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchDrlEventListByEntryCalled = true;
        $this->fetchDrlEventListByEntryCallCount++;
        if ($this->fetchDrlEventListByEntryException) {
            throw $this->fetchDrlEventListByEntryException;
        }
        return $this->fetchDrlEventListByEntryValue;
    }

    public function hasFetchDrlEventListByEntryBeenCalled(): bool
    {
        return $this->fetchDrlEventListByEntryCalled;
    }

    /**
     * @return int
     */
    public function getFetchDrlEventListByEntryCallCount(): int
    {
        return $this->fetchDrlEventListByEntryCallCount;
    }

    /**
     * @param CleanArchitectureException|null $fetchDrlEventListByEntryException
     */
    public function setFetchDrlEventListByEntryException(
        ?CleanArchitectureException $fetchDrlEventListByEntryException
    ): void {
        $this->fetchDrlEventListByEntryException =
            $fetchDrlEventListByEntryException;
    }

    /**
     * @param DrlEventEntity[] $fetchDrlEventListByEntryValue
     */
    public function setFetchDrlEventListByEntryValue(
        array $fetchDrlEventListByEntryValue
    ): void {
        $this->fetchDrlEventListByEntryValue = $fetchDrlEventListByEntryValue;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventListByTotalFaults(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchDrlEventListByTotalFaultsCalled = true;
        $this->fetchDrlEventListByTotalFaultsCallCount++;
        if ($this->fetchDrlEventListByTotalFaultsException) {
            throw $this->fetchDrlEventListByTotalFaultsException;
        }
        return $this->fetchDrlEventListByTotalFaultsValue;
    }

    /**
     * @return bool
     */
    public function hasFetchDrlEventListByTotalFaultsBeenCalled(): bool
    {
        return $this->fetchDrlEventListByTotalFaultsCalled;
    }

    /**
     * @return int
     */
    public function getFetchDrlEventListByTotalFaultsCallCount(): int
    {
        return $this->fetchDrlEventListByTotalFaultsCallCount;
    }

    /**
     * @param CleanArchitectureException|null $fetchDrlEventListByTotalFaultsException
     */
    public function setFetchDrlEventListByTotalFaultsException(
        ?CleanArchitectureException $fetchDrlEventListByTotalFaultsException
    ): void {
        $this->fetchDrlEventListByTotalFaultsException =
            $fetchDrlEventListByTotalFaultsException;
    }

    /**
     * @param DrlEventEntity[] $fetchDrlEventListByTotalFaultsValue
     */
    public function setFetchDrlEventListByTotalFaultsValue(
        array $fetchDrlEventListByTotalFaultsValue
    ): void {
        $this->fetchDrlEventListByTotalFaultsValue =
            $fetchDrlEventListByTotalFaultsValue;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventListByMeanFaults(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchDrlEventListByMeanFaultsCalled = true;
        $this->fetchDrlEventListByMeanFaultsCallCount++;
        if ($this->fetchDrlEventListByMeanFaultsException) {
            throw $this->fetchDrlEventListByMeanFaultsException;
        }
        return $this->fetchDrlEventListByMeanFaultsValue;
    }

    /**
     * @return bool
     */
    public function hasFetchDrlEventListByMeanFaultsBeenCalled(): bool
    {
        return $this->fetchDrlEventListByMeanFaultsCalled;
    }

    /**
     * @return int
     */
    public function getFetchDrlEventListByMeanFaultsCallCount(): int
    {
        return $this->fetchDrlEventListByMeanFaultsCallCount;
    }

    /**
     * @param CleanArchitectureException|null $exception
     */
    public function setFetchDrlEventListByMeanFaultsException(
        ?CleanArchitectureException $exception
    ): void {
        $this->fetchDrlEventListByMeanFaultsException =
            $exception;
    }

    /**
     * @param DrlEventEntity[] $fetchDrlEventListByMeanFaultsValue
     */
    public function setFetchDrlEventListByMeanFaultsValue(
        array $fetchDrlEventListByMeanFaultsValue
    ): void {
        $this->fetchDrlEventListByMeanFaultsValue =
            $fetchDrlEventListByMeanFaultsValue;
    }

    /**
     * @param RecordRequestOptionsEntity $inputData
     * @inheritDoc
     */
    public function fetchDrlEventsListByVictoryMargin(
        RecordRequestOptionsEntity $inputData
    ): array {
        $this->fetchDrlEventListByVictoryMarginCalled = true;
        $this->fetchDrlEventListByVictoryMarginCallCount++;
        if ($this->fetchDrlEventListByVictoryMarginException) {
            throw $this->fetchDrlEventListByVictoryMarginException;
        }
        return $this->fetchDrlEventListByVictoryMarginValue;
    }

    /**
     * @return bool
     */
    public function hasFetchDrlEventListByVictoryMarginBeenCalled(): bool
    {
        return $this->fetchDrlEventListByVictoryMarginCalled;
    }

    /**
     * @return int
     */
    public function getFetchDrlEventListByVictoryMarginCallCount(): int
    {
        return $this->fetchDrlEventListByVictoryMarginCallCount;
    }

    /**
     * @param CleanArchitectureException|null $fetchDrlEventListByVictoryMarginException
     */
    public function setFetchDrlEventListByVictoryMarginException(
        ?CleanArchitectureException $fetchDrlEventListByVictoryMarginException
    ): void {
        $this->fetchDrlEventListByVictoryMarginException =
            $fetchDrlEventListByVictoryMarginException;
    }

    /**
     * @param DrlEventEntity[] $fetchDrlEventListByVictoryMarginValue
     */
    public function setFetchDrlEventListByVictoryMarginValue(
        array $fetchDrlEventListByVictoryMarginValue
    ): void {
        $this->fetchDrlEventListByVictoryMarginValue =
            $fetchDrlEventListByVictoryMarginValue;
    }


}
