<?php
declare(strict_types=1);

namespace mocks;


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
}