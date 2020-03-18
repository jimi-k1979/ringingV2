<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\UserEntity;

interface UserRepositoryInterface
{
    public const GUEST_USER = 0;

    public function getLoggedInUser(): UserEntity;
}