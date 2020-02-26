<?php

declare(strict_types=1);

namespace traits;


use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\LocationEntity;

trait CreateMockDrlEventTrait
{

    private function createMockDrlEvent(): DrlEventEntity
    {
        $entity = new DrlEventEntity();
        $entity->setId(1234);
        $entity->setLocation(new LocationEntity());
        $entity->setCompetition(new DrlCompetitionEntity());
        $entity->setYear('1970');

        return $entity;
    }

}