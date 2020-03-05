<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DrlEventEntity;

interface EventRepositoryInterface
{
    const UNABLE_TO_INSERT_EXCEPTION = 2501;
    const NO_ROWS_FOUND_EXCEPTION = 2502;
    const NO_ROWS_UPDATED = 2503;
    const NO_ROWS_DELETED = 2504;

    public function insertEvent(
        DrlEventEntity $entity
    ): DrlEventEntity;

    public function selectCompetition(int $id): DrlEventEntity;

}