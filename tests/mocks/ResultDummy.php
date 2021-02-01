<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use DrlArchive\traits\CreateMockDrlResultTrait;

class ResultDummy implements ResultRepositoryInterface
{

    use CreateMockDrlResultTrait;

    public function insertDrlResult(DrlResultEntity $result): void
    {
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventResults(DrlEventEntity $event): array
    {
        return $this->createMockEventResults();
    }
}
