<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\traits\CreateMockDrlEventTrait;

class EventDummy implements EventRepositoryInterface
{
    use CreateMockDrlEventTrait;

    public function insertDrlEvent(DrlEventEntity $entity): void
    {
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
}