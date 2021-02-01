<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\TestConstants;

trait CreateMockDrlCompetitionTrait
{
    private function createMockDrlCompetition(): DrlCompetitionEntity
    {
        $entity = new DrlCompetitionEntity();
        $entity->setId(TestConstants::TEST_COMPETITION_ID);
        $entity->setName(TestConstants::TEST_COMPETITION_NAME);
        $entity->setSingleTowerCompetition(
            TestConstants::TEST_SINGLE_TOWER_COMPETITION
        );

        return $entity;
    }
}
