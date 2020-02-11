<?php
declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories;


use DrlArchive\core\interfaces\factories\repositories\DeaneryRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use DrlArchive\implementation\repositories\sql\Database;
use DrlArchive\implementation\repositories\sql\DeanerySql;

class DeaneryRepositoryFactory implements DeaneryRepositoryFactoryInterface
{

    public function create(): DeaneryRepositoryInterface
    {
        return new DeanerySql(Database::createConnection());
    }
}