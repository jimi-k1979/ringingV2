<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;
use DrlArchive\traits\CreateMockDrlEventTrait;
use DrlArchive\traits\CreateMockJudgeTrait;
use DrlArchive\traits\CreateMockLocationTrait;

class JudgeDummy implements JudgeRepositoryInterface
{

    use CreateMockJudgeTrait;
    use CreateMockDrlEventTrait;

    public function fetchJudgesByDrlEvent(DrlEventEntity $entity): array
    {
        return [$this->createMockJudge()];
    }

    /**
     * @inheritDoc
     */
    public function fetchJudgeById(int $id): JudgeEntity
    {
        return $this->createMockJudge();
    }

    /**
     * @inheritDoc
     */
    public function fetchJudgeDrlEventList(JudgeEntity $judge): array
    {
        return [$this->createMockDrlEvent()];
    }
}
