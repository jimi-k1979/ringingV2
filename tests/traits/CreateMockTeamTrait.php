<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\TeamEntity;
use DrlArchive\TestConstants;

trait CreateMockTeamTrait
{
    use CreateMockDeaneryTrait;

    private function createMockTeam(): TeamEntity
    {
        $teamEntity = new TeamEntity();
        $teamEntity->setId(TestConstants::TEST_TEAM_ID);
        $teamEntity->setName(TestConstants::TEST_TEAM_NAME);
        $teamEntity->setDeanery($this->createMockDeanery());

        return $teamEntity;
    }
}