<?php
declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories;


use DrlArchive\core\interfaces\factories\repositories\LocationRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use DrlArchive\implementation\repositories\sql\Database;
use DrlArchive\implementation\repositories\sql\LocationSql;

class LocationRepositoryFactory implements LocationRepositoryFactoryInterface
{

    public function create(): LocationRepositoryInterface
    {
        return new LocationSql(Database::createConnection());
    }
}