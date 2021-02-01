<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;
use DrlArchive\traits\CreateMockJudgeTrait;

class JudgeDummy implements JudgeRepositoryInterface
{

    use CreateMockJudgeTrait;

    public function fetchJudgesByDrlEvent(DrlEventEntity $entity): array
    {
        return [$this->createMockJudge()];
    }
}