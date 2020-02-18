<?php
declare(strict_types=1);

namespace traits;


use DrlArchive\core\entities\CompetitionEntity;

trait CreateMockCompetitionTrait
{
    private function createMockCompetition(): CompetitionEntity
    {
        $entity = new CompetitionEntity();
        $entity->setId(999);
        $entity->setName('Test competition');
        $entity->setSingleTowerCompetition(true);

        return $entity;
    }
}