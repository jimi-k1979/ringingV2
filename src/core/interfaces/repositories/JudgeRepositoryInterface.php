<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;

interface JudgeRepositoryInterface
{
    public const UNABLE_TO_CREATE_EXCEPTION = 2901;
    public const NO_RECORDS_FOUND_EXCEPTION = 2902;
    public const NO_RECORDS_UPDATED_EXCEPTION = 2903;
    public const NO_RECORDS_DELETED_EXCEPTION = 2904;

    /**
     * @param DrlEventEntity $entity
     * @return JudgeEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchJudgesByDrlEvent(DrlEventEntity $entity): array;

}
