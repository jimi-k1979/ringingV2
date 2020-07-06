<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\traits\CreateMockDrlEventTrait;

class EventSpy implements EventRepositoryInterface
{
    use CreateMockDrlEventTrait;

    /**
     * @var bool
     */
    private $insertEventCalled = false;
    /**
     * @var bool
     */
    private $throwException = false;
    /**
     * @var DrlEventEntity|null
     */
    private $drlEventValue;
    /**
     * @var bool
     */
    private $fetchDrlEventCalled = false;
    /**
     * @var bool
     */
    private $fetchEventThrowsException = false;
    /**
     * @var DrlEventEntity
     */
    private $fetchDrlEventValue;
    /**
     * @var bool
     */
    private $fetchDrlEventsByCompetitionIdCalled = false;
    /**
     * @var DrlEventEntity[]
     */
    private $fetchDrlEventsByCompetitionIdValue = [];
    /**
     * @var bool
     */
    private $fetchDrlEventsByCompetitionAndLocationIdsCalled = false;
    /**
     * @var bool
     */
    private $fetchDrlEventsByCompetitionAndLocationIdsThrowsException = false;
    /**
     * @var DrlEventEntity[]
     */
    private $fetchDrlEventsByCompetitionAndLocationIdsValue = [];
    /**
     * @var bool
     */
    private $fetchDrlEventsByYearCalled = false;
    /**
     * @var bool
     */
    private $fetchDrlEventsByYearThrowsException = false;
    /**
     * @var DrlEventEntity[]
     */
    private $fetchDrlEventsByYearValue = [];
    /**
     * @var bool
     */
    private $fetchDrlEventByYearAndCompetitionNameCalled = false;
    /**
     * @var bool
     */
    private $fetchDrlEventByYearAndCompetitionNameThrowsException = false;
    /**
     * @var DrlEventEntity
     */
    private $fetchDrlEventByYearAndCompetitionNameValue;


    public function setThrowException(): void
    {
        $this->throwException = true;
    }

    /**
     * @param DrlEventEntity $entity
     * @return DrlEventEntity
     * @throws GeneralRepositoryErrorException
     */
    public function insertDrlEvent(DrlEventEntity $entity): DrlEventEntity
    {
        $this->insertEventCalled = true;
        if ($this->throwException) {
            throw new GeneralRepositoryErrorException(
                "Can't insert event",
                EventRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }

        return $this->drlEventValue ?? $this->createMockDrlEvent();
    }

    /**
     * @return bool
     */
    public function hasInsertEventBeenCalled(): bool
    {
        return $this->insertEventCalled;
    }

    /**
     * @param DrlEventEntity|null $drlEventValue
     */
    public function setDrlEventValue(?DrlEventEntity $drlEventValue): void
    {
        $this->drlEventValue = $drlEventValue;
    }

    /**
     * @param int $id
     * @return DrlEventEntity
     * @throws RepositoryNoResults
     */
    public function fetchDrlEvent(int $id): DrlEventEntity
    {
        $this->fetchDrlEventCalled = true;
        if ($this->fetchEventThrowsException) {
            throw new RepositoryNoResults(
                'No drl event found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchDrlEventValue ?? $this->createMockDrlEvent();
    }

    /**
     */
    public function setFetchEventThrowsException(): void
    {
        $this->fetchEventThrowsException = true;
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
     * @param int $competitionId
     * @return DrlEventEntity[]
     */
    public function fetchDrlEventsByCompetitionId(int $competitionId): array
    {
        $this->fetchDrlEventsByCompetitionIdCalled = true;
        if ($this->throwException) {
            throw new RepositoryNoResults(
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

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByCompetitionAndLocationIds(int $competitionId, int $locationId): array
    {
        $this->fetchDrlEventsByCompetitionAndLocationIdsCalled = true;
        if ($this->fetchDrlEventsByCompetitionAndLocationIdsThrowsException) {
            throw new RepositoryNoResults(
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
            throw new RepositoryNoResults(
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
            throw new RepositoryNoResults(
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

}