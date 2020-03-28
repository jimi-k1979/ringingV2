<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\UserEntity;

interface UserRepositoryInterface
{
    public const NO_USER_ID_GIVEN_EXCEPTION = 2605;

    public const GUEST_USER = 0;

    public function getLoggedInUser(): UserEntity;
}