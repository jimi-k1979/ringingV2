<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
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
}
