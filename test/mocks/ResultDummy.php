<?php

declare(strict_types=1);

namespace test\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use test\traits\CreateMockDrlResultTrait;

class ResultDummy implements ResultRepositoryInterface
{

    use CreateMockDrlResultTrait;

    public function insertDrlResult(DrlResultEntity $resultEntity): DrlResultEntity
    {
        return $this->createMockDrlResult();
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventResults(DrlEventEntity $eventEntity): array
    {
        return $this->createMockEventResults();
    }
}