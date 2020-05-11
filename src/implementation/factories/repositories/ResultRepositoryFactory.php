<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories;


use DrlArchive\core\interfaces\factories\repositories\ResultRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use DrlArchive\implementation\repositories\sql\Database;
use DrlArchive\implementation\repositories\sql\ResultSql;

class ResultRepositoryFactory implements ResultRepositoryFactoryInterface
{

    public function create(): ResultRepositoryInterface
    {
        return new ResultSql(Database::createConnection());
    }
}