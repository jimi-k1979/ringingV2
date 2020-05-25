<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories;


use DrlArchive\core\interfaces\factories\repositories\EventRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\implementation\repositories\sql\Database;
use DrlArchive\implementation\repositories\sql\EventSql;

class EventRepositoryFactory implements EventRepositoryFactoryInterface
{

    public function create(): EventRepositoryInterface
    {
        return new EventSql(Database::createConnection());
    }
}