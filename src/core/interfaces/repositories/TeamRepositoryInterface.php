<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\TeamEntity;

interface TeamRepositoryInterface
{
    const UNABLE_TO_INSERT_EXCEPTION = 2201;
    const NO_ROWS_FOUND_EXCEPTION = 2202;
    const NO_ROWS_UPDATED = 2203;
    const NO_ROWS_DELETED = 2204;

    public function insertTeam(TeamEntity $teamEntity): TeamEntity;

    public function selectTeam(int $teamId): TeamEntity;

    public function updateTeam(TeamEntity $teamEntity): TeamEntity;

    public function deleteTeam(TeamEntity $teamEntity): bool;

    /**
     * @param string $searchTerm
     * @return TeamEntity[]
     */
    public function fuzzySearchTeam(string $searchTerm): array;
}