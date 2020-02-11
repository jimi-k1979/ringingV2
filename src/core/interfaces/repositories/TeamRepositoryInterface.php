<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\TeamEntity;

interface TeamRepositoryInterface
{
    public function insertTeam(): TeamEntity;

    public function selectTeam(): TeamEntity;

    public function updateTeam(): TeamEntity;

    public function deleteTeam(): bool;
}