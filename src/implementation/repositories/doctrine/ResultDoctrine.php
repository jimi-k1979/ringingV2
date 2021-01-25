<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;

class ResultDoctrine extends DoctrineRepository
    implements ResultRepositoryInterface
{

    public function insertDrlResult(DrlResultEntity $resultEntity): void
    {
        // TODO: Implement insertDrlResult() method.
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventResults(DrlEventEntity $eventEntity): array
    {
        // TODO: Implement fetchDrlEventResults() method.
    }
}
