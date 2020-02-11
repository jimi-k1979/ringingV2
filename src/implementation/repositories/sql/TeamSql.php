<?php
declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;

class TeamSql extends MysqlRepository implements TeamRepositoryInterface
{

    public function insertTeam(): TeamEntity
    {
        // TODO: Implement insertTeam() method.
    }

    public function selectTeam(): TeamEntity
    {
        // TODO: Implement selectTeam() method.
    }

    public function updateTeam(): TeamEntity
    {
        // TODO: Implement updateTeam() method.
    }

    public function deleteTeam(): bool
    {
        // TODO: Implement deleteTeam() method.
    }
}