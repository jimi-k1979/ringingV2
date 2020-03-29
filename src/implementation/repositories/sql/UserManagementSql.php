<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\interfaces\repositories\UserManagementRepositoryInterface;

class UserManagementSql
    extends MysqlRepository
    implements UserManagementRepositoryInterface
{

    public function fetchById(int $userId): UserEntity
    {
        // TODO: Implement fetchById() method.
    }
}