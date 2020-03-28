<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories;


use DrlArchive\core\interfaces\factories\repositories\UserManagementRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserManagementRepositoryInterface;
use DrlArchive\implementation\repositories\sql\Database;
use DrlArchive\implementation\repositories\sql\UserManagementSql;

class UserManagementRepositoryFactory
    implements UserManagementRepositoryFactoryInterface
{

    public function create(): UserManagementRepositoryInterface
    {
        return new UserManagementSql(Database::createConnection());
    }
}