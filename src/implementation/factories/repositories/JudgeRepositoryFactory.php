<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories;


use DrlArchive\core\interfaces\factories\repositories\JudgeRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;
use DrlArchive\implementation\repositories\sql\Database;
use DrlArchive\implementation\repositories\sql\JudgeSql;

class JudgeRepositoryFactory implements JudgeRepositoryFactoryInterface
{

    public function create(): JudgeRepositoryInterface
    {
        return new JudgeSql(Database::createConnection());
    }
}