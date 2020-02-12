<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\TeamEntity;

interface TeamRepositoryInterface
{
    public function insertTeam(TeamEntity $teamEntity): TeamEntity;

    public function selectTeam(int $teamId): TeamEntity;

    public function updateTeam(TeamEntity $teamEntity): TeamEntity;

    public function deleteTeam(TeamEntity $teamEntity): bool;
}