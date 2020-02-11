<?php
declare(strict_types=1);

namespace traits;


use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\TeamEntity;

trait CreateMockTeamTrait
{
    private function createMockTeam(): TeamEntity
    {
        $teamEntity = new TeamEntity();
        $teamEntity->setId(123);
        $teamEntity->setName('Test team');
        $teamEntity->setDeanery(new DeaneryEntity());

        return $teamEntity;
    }
}