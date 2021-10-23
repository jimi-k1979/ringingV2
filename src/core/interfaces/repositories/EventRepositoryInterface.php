<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;

interface EventRepositoryInterface
{
    public const NO_ROWS_CREATED_EXCEPTION = 2501;
    public const NO_ROWS_FOUND_EXCEPTION = 2502;
    public const NO_ROWS_UPDATED_EXCEPTION = 2503;
    public const NO_ROWS_DELETED_EXCEPTION = 2504;

    public const INVALID_EVENT_TYPE_EXCEPTION = 2505;

    /**
     * @param DrlEventEntity $entity
     * @throws CleanArchitectureException
     */
    public function insertDrlEvent(
        DrlEventEntity $entity
    ): void;

    /**
     * @param int $id
     * @return DrlEventEntity
     * @throws CleanArchitectureException
     */
    public function fetchDrlEvent(int $id): DrlEventEntity;

    /**
     * @param int $competitionId
     * @return DrlEventEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchDrlEventsByCompetitionId(int $competitionId): array;

    /**
     * @param int $competitionId
     * @param int $locationId
     * @return DrlEventEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchDrlEventsByCompetitionAndLocationIds(
        int $competitionId,
        int $locationId
    ): array;

    /**
     * @param string $year
     * @return DrlEventEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchDrlEventsByYear(string $year): array;

    /**
     * @param string $year
     * @param string $competitionName
     * @return DrlEventEntity
     * @throws CleanArchitectureException
     */
    public function fetchDrlEventByYearAndCompetitionName(
        string $year,
        string $competitionName
    ): DrlEventEntity;

    /**
     * @param string $year
     * @param int $competitionId
     * @return DrlEventEntity
     * @throws CleanArchitectureException | RepositoryNoResultsException
     */
    public function fetchDrlEventByYearAndCompetitionId(
        string $year,
        int $competitionId
    ): DrlEventEntity;

    /**
     * @param string $name
     * @return DrlEventEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchDrlEventsByCompetitionName(string $name): array;

    /**
     * @param int $competitionId
     * @param string $locationName
     * @return DrlEventEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchDrlEventsByCompetitionIdAndVenue(
        int $competitionId,
        string $locationName
    ): array;

    /**
     * @param DrlEventEntity $event
     * @throws CleanArchitectureException
     */
    public function fetchSingleDrlEventStatistics(DrlEventEntity $event): void;

    /**
     * @param JudgeEntity $judge
     * @return DrlEventEntity[]
     */
    public function fetchDrlEventListByJudge(JudgeEntity $judge): array;
}
