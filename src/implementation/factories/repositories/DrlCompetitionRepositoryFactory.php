<?php
declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories;


use DrlArchive\core\interfaces\factories\repositories\DrlCompetitionRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\DrlCompetitionRepositoryInterface;
use DrlArchive\implementation\repositories\sql\DrlCompetitionSql;
use DrlArchive\implementation\repositories\sql\Database;

class DrlCompetitionRepositoryFactory implements DrlCompetitionRepositoryFactoryInterface
{

    public function create(): DrlCompetitionRepositoryInterface
    {
        return new DrlCompetitionSql(Database::createConnection());
    }
}