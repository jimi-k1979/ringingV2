<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\TeamEntity;

trait CreateMockTeamTrait
{
    use CreateMockDeaneryTrait;

    private function createMockTeam(): TeamEntity
    {
        $teamEntity = new TeamEntity();
        $teamEntity->setId(123);
        $teamEntity->setName('Test team');
        $teamEntity->setDeanery($this->createMockDeanery());

        return $teamEntity;
    }
}