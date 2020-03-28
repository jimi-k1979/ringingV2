<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\factories\repositories;


use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;

interface UserRepositoryFactoryInterface
{
    public function create(?int $userId = null): UserRepositoryInterface;
}