<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;

class ResultSql extends MysqlRepository implements ResultRepositoryInterface
{

    public function insertDrlResult(
        DrlResultEntity $resultEntity
    ): DrlResultEntity {
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