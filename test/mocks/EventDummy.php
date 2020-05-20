<?php

declare(strict_types=1);

namespace test\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use traits\CreateMockDrlEventTrait;

class EventDummy implements EventRepositoryInterface
{
    use CreateMockDrlEventTrait;

    public function insertDrlEvent(DrlEventEntity $entity): DrlEventEntity
    {
        return $this->createMockDrlEvent();
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
}