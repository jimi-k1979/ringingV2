<?php
declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories;


use DrlArchive\core\interfaces\factories\repositories\CompetitionRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\implementation\repositories\sql\CompetitionSql;
use DrlArchive\implementation\repositories\sql\Database;

class CompetitionRepositoryFactory implements CompetitionRepositoryFactoryInterface
{

    public function create(): CompetitionRepositoryInterface
    {
        return new CompetitionSql(Database::createConnection());
    }
}