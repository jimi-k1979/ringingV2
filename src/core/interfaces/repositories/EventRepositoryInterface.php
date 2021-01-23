<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;

interface EventRepositoryInterface
{
    public const NO_ROWS_CREATED_EXCEPTION = 2501;
    public const NO_ROWS_FOUND_EXCEPTION = 2502;
    public const NO_ROWS_UPDATED_EXCEPTION = 2503;
    public const NO_ROWS_DELETED_EXCEPTION = 2504;

    public const INVALID_EVENT_TYPE_EXCEPTION = 2505;

    public function insertDrlEvent(
        DrlEventEntity $entity
    ): void;

    /**
     * @param int $id
     * @return DrlEventEntity
     * @throws RepositoryNoResults
     */
    public function fetchDrlEvent(int $id): DrlEventEntity;

    /**
     * @param int $competitionId
     * @return DrlEventEntity[]
     * @throws RepositoryNoResults
     */
    public function fetchDrlEventsByCompetitionId(int $competitionId): array;

    /**
     * @param int $competitionId
     * @param int $locationId
     * @return DrlEventEntity[]
     * @throws RepositoryNoResults
     */
    public function fetchDrlEventsByCompetitionAndLocationIds(
        int $competitionId,
        int $locationId
    ): array;

    /**
     * @param string $year
     * @return DrlEventEntity[]
     * @throws RepositoryNoResults
     */
    public function fetchDrlEventsByYear(string $year): array;

    /**
     * @param string $year
     * @param string $competitionName
     * @return DrlEventEntity
     * @throws RepositoryNoResults
     */
    public function fetchDrlEventByYearAndCompetitionName(
        string $year,
        string $competitionName
    ): DrlEventEntity;

    public function fetchDrlEventByYearAndCompetitionId(
        string $year,
        int $competitionId
    ): DrlEventEntity;
}
