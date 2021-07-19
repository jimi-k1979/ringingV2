<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockDrlEventTrait;

class EventDummy implements EventRepositoryInterface
{
    use CreateMockDrlEventTrait;

    public function insertDrlEvent(DrlEventEntity $entity): void
    {
        $entity->setId(TestConstants::TEST_EVENT_ID);
    }

    public function fetchDrlEvent(int $id): DrlEventEntity
    {
        return $this->createMockDrlEvent();
    }

    /**
     * @param int $competitionId
     * @return DrlEventEntity[]
     */
    public function fetchDrlEventsByCompetitionId(int $competitionId): array
    {
        return [$this->createMockDrlEvent()];
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByCompetitionAndLocationIds(
        int $competitionId,
        int $locationId
    ): array {
        return [$this->createMockDrlEvent()];
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByYear(string $year): array
    {
        return [$this->createMockDrlEvent()];
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventByYearAndCompetitionName(
        string $year,
        string $competitionName
    ): DrlEventEntity {
        return $this->createMockDrlEvent();
    }

    public function fetchDrlEventByYearAndCompetitionId(
        string $year,
        int $competitionId
    ): DrlEventEntity {
        return $this->createMockDrlEvent();
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByCompetitionName(string $name): array
    {
        return [$this->createMockDrlEvent()];
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByCompetitionIdAndVenue(
        int $competitionId,
        string $locationName
    ): array {
        return [$this->createMockDrlEvent()];
    }

    /**
     * @inheritDoc
     */
    public function fetchSingleDrlEventStatistics(DrlEventEntity $event): void
    {
        $event->setTotalFaults(TestConstants::TEST_EVENT_TOTAL_FAULTS);
        $event->setMeanFaults(TestConstants::TEST_EVENT_MEAN_FAULTS);
        $event->setWinningMargin(TestConstants::TEST_EVENT_WINNING_MARGIN);
    }
}
