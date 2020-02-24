<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\interfaces\repositories\DrlEventRepositoryInterface;

class DrlEventDummy implements DrlEventRepositoryInterface
{

    public function insertEvent(DrlEventEntity $entity): DrlEventEntity
    {
        return new DrlEventEntity();
    }

    public function selectCompetition(int $id): DrlEventEntity
    {
        return new DrlEventEntity();
    }
}