<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\OtherCompetitionEntity;

trait CreateMockOtherCompetitionTrait
{
    private function createMockOtherCompetition(): OtherCompetitionEntity
    {
        $entity = new OtherCompetitionEntity();
        $entity->setId(888);
        $entity->setName('Other competition');
        $entity->setSingleTowerCompetition(true);

        return $entity;
    }
}