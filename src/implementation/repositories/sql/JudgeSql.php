<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;

class JudgeSql extends MysqlRepository implements JudgeRepositoryInterface
{

    public function fetchJudgesByDrlEvent(DrlEventEntity $id): array
    {
        // TODO: Implement fetchJudge() method.
    }
}