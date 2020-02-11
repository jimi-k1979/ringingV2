<?php
declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories;


use DrlArchive\core\interfaces\factories\repositories\TeamRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use DrlArchive\implementation\repositories\sql\Database;
use DrlArchive\implementation\repositories\sql\TeamSql;

class TeamRepositoryFactory
    implements TeamRepositoryFactoryInterface
{

    public function create(): TeamRepositoryInterface
    {
        return new TeamSql(Database::createConnection());
    }

}