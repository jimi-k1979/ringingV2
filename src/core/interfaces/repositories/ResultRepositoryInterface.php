<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;

interface ResultRepositoryInterface
{
    public const UNABLE_TO_INSERT_EXCEPTION = 2301;
    public const NO_ROWS_FOUND_EXCEPTION = 2302;

    public function insertDrlResult(
        DrlResultEntity $resultEntity
    ): void;

    /**
     * @param DrlEventEntity $eventEntity
     * @return DrlResultEntity[]
     */
    public function fetchDrlEventResults(
        DrlEventEntity $eventEntity
    ): array;
}
