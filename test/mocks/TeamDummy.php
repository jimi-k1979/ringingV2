<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use traits\CreateMockTeamTrait;

class TeamDummy implements TeamRepositoryInterface
{
    use CreateMockTeamTrait;

    public function insertTeam(): TeamEntity
    {
        return $this->createMockTeam();
    }

    public function selectTeam(): TeamEntity
    {
        return $this->createMockTeam();
    }

    public function updateTeam(): TeamEntity
    {
        return $this->createMockTeam();
    }

    public function deleteTeam(): bool
    {
        return true;
    }
}