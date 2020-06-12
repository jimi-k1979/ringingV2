<?php

declare(strict_types=1);

namespace traits;


use DrlArchive\core\entities\DrlCompetitionEntity;

trait CreateMockDrlCompetitionTrait
{
    private function createMockDrlCompetition(): DrlCompetitionEntity
    {
        $entity = new DrlCompetitionEntity();
        $entity->setId(999);
        $entity->setName('Test competition');
        $entity->setSingleTowerCompetition(true);

        return $entity;
    }
}