<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DrlEventEntity;

interface EventRepositoryInterface
{
    public const UNABLE_TO_INSERT_EXCEPTION = 2501;
    public const NO_ROWS_FOUND_EXCEPTION = 2502;
    public const NO_ROWS_UPDATED_EXCEPTION = 2503;
    public const NO_ROWS_DELETED_EXCEPTION = 2504;

    public const INVALID_EVENT_TYPE_EXCEPTION = 2505;

    public function insertDrlEvent(
        DrlEventEntity $entity
    ): DrlEventEntity;

    public function fetchDrlEvent(int $id): DrlEventEntity;

    /**
     * @param int $competitionId
     * @return DrlEventEntity[]
     */
    public function fetchDrlEventsByCompetitionId(int $competitionId): array;

}