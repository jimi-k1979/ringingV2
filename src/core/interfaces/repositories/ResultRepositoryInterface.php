<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;

interface ResultRepositoryInterface
{
    public const UNABLE_TO_INSERT_EXCEPTION = 2301;
    public const NO_ROWS_FOUND_EXCEPTION = 2302;

    /**
     * @param DrlResultEntity $result
     * @throws CleanArchitectureException
     */
    public function insertDrlResult(
        DrlResultEntity $result
    ): void;

    /**
     * @param DrlEventEntity $event
     * @return DrlResultEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchDrlEventResults(
        DrlEventEntity $event
    ): array;
}
