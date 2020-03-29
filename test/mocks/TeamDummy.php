<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use traits\CreateMockTeamTrait;

class TeamDummy implements TeamRepositoryInterface
{
    use CreateMockTeamTrait;

    public function insertTeam(TeamEntity $teamEntity): TeamEntity
    {
        return $this->createMockTeam();
    }

    public function selectTeam(int $teamId): TeamEntity
    {
        return $this->createMockTeam();
    }

    public function updateTeam(TeamEntity $teamEntity): TeamEntity
    {
        return $this->createMockTeam();
    }

    public function deleteTeam(TeamEntity $teamEntity): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchTeam(string $searchTerm): array
    {
        return [];
    }
}