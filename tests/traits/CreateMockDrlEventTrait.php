<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\DrlEventEntity;

trait CreateMockDrlEventTrait
{
    use CreateMockLocationTrait;
    use CreateMockDrlCompetitionTrait;

    private function createMockDrlEvent(): DrlEventEntity
    {
        $entity = new DrlEventEntity();
        $entity->setId(1234);
        $entity->setLocation($this->createMockLocation());
        $entity->setCompetition($this->createMockDrlCompetition());
        $entity->setYear('1970');

        return $entity;
    }

}