<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\interfaces\repositories\DrlEventRepositoryInterface;
use traits\CreateMockDrlEventTrait;

class DrlEventDummy implements DrlEventRepositoryInterface
{
    use CreateMockDrlEventTrait;

    public function insertEvent(DrlEventEntity $entity): DrlEventEntity
    {
        return $this->createMockDrlEvent();
    }

    public function selectCompetition(int $id): DrlEventEntity
    {
        return $this->createMockDrlEvent();
    }
}