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
        $teamEntity->setEarliestYear(TestConstants::TEST_TEAM_EARLIEST_YEAR);
        $teamEntity->setLatestYear(TestConstants::TEST_TEAM_MOST_RECENT_YEAR);

        return $teamEntity;
    }
}
