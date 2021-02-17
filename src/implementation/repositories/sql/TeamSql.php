<?php
declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;

/**
 * Class TeamSql
 * @package DrlArchive\implementation\repositories\sql
 * @deprecated
 */
class TeamSql extends MysqlRepository implements TeamRepositoryInterface
{

    public const SELECT_TEAM_NAME = 't.teamName';

    public const FIELD_NAME_TEAM_NAME = 'teamName';

    public function insertTeam(TeamEntity $teamEntity): void
    {
        // TODO: Implement insertTeam() method.
    }

    public function selectTeam(int $teamId): TeamEntity
    {
        return new TeamEntity();
    }

    public function updateTeam(TeamEntity $teamEntity): void
    {
        // TODO: Implement updateTeam() method.
    }

    public function deleteTeam(TeamEntity $teamEntity): bool
    {
        return false;
    }

    public function fuzzySearchTeam(string $searchTerm): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamByName(string $teamName): TeamEntity
    {
        return new TeamEntity();
    }
}
