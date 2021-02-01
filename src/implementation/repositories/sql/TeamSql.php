<?php
declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;

class TeamSql extends MysqlRepository implements TeamRepositoryInterface
{

    public const SELECT_TEAM_NAME = 't.teamName';

    public const FIELD_NAME_TEAM_NAME = 'teamName';

    public function insertTeam(TeamEntity $teamEntity): TeamEntity
    {
        // TODO: Implement insertTeam() method.
    }

    public function selectTeam(int $teamId): TeamEntity
    {
        // TODO: Implement selectTeam() method.
    }

    public function updateTeam(TeamEntity $teamEntity): TeamEntity
    {
        // TODO: Implement updateTeam() method.
    }

    public function deleteTeam(TeamEntity $teamEntity): bool
    {
        // TODO: Implement deleteTeam() method.
    }

    public function fuzzySearchTeam(string $searchTerm): array
    {
        // TODO: Implement fuzzySearchTeam() method.
    }
}