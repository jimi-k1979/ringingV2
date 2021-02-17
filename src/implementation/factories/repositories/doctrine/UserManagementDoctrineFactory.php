<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories\doctrine;


use DrlArchive\core\interfaces\factories\repositories\UserManagementRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserManagementRepositoryInterface;
use DrlArchive\implementation\repositories\doctrine\DoctrineDatabase;
use DrlArchive\implementation\repositories\doctrine\UserManagementDoctrine;

class UserManagementDoctrineFactory implements
    UserManagementRepositoryFactoryInterface
{

    public function create(): UserManagementRepositoryInterface
    {
        return new UserManagementDoctrine(DoctrineDatabase::createConnection());
    }
}
