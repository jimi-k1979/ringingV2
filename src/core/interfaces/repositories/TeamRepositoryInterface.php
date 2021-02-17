<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;

interface TeamRepositoryInterface
{
    public const UNABLE_TO_INSERT_EXCEPTION = 2201;
    public const NO_ROWS_FOUND_EXCEPTION = 2202;
    public const NO_ROWS_UPDATED = 2203;
    public const NO_ROWS_DELETED = 2204;

    /**
     * @param TeamEntity $teamEntity
     * @throws CleanArchitectureException
     */
    public function insertTeam(TeamEntity $teamEntity): void;

    /**
     * @param int $teamId
     * @return TeamEntity
     * @throws CleanArchitectureException
     */
    public function selectTeam(int $teamId): TeamEntity;

    /**
     * @param TeamEntity $teamEntity
     * @throws CleanArchitectureException
     */
    public function updateTeam(TeamEntity $teamEntity): void;

    /**
     * @param string $searchTerm
     * @return TeamEntity[]
     * @throws CleanArchitectureException
     */
    public function fuzzySearchTeam(string $searchTerm): array;

    /**
     * @param string $teamName
     * @return TeamEntity
     * @throws CleanArchitectureException
     */
    public function fetchTeamByName(string $teamName): TeamEntity;
}
