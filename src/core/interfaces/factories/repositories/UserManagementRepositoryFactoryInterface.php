<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\factories\repositories;


use DrlArchive\core\interfaces\repositories\UserManagementRepositoryInterface;

interface UserManagementRepositoryFactoryInterface
{
    public function create(): UserManagementRepositoryInterface;
}