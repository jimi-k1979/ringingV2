<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\OtherCompetitionEntity;
use DrlArchive\TestConstants;

trait CreateMockOtherCompetitionTrait
{
    private function createMockOtherCompetition(): OtherCompetitionEntity
    {
        $entity = new OtherCompetitionEntity();
        $entity->setId(TestConstants::TEST_COMPETITION_ID);
        $entity->setName(TestConstants::TEST_COMPETITION_NAME);
        $entity->setSingleTowerCompetition(
            TestConstants::TEST_SINGLE_TOWER_COMPETITION
        );

        return $entity;
    }
}
