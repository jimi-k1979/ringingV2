<?php

declare(strict_types=1);

namespace traits;


use DrlArchive\core\entities\DrlEventEntity;

trait CreateMockDrlEventTrait
{
    use CreateMockLocationTrait;
    use CreateMockDrlCompetitionTrait;

    private function createMockDrlEvent(): DrlEventEntity
    {
        $entity = new DrlEventEntity();
        $entity->setId(1234);
        $entity->setLocation($this->CreateMockLocation());
        $entity->setCompetition($this->createMockDrlCompetition());
        $entity->setYear('1970');

        return $entity;
    }

}