<?php

declare(strict_types=1);

namespace mocks;

use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;

class GuestUserDummy implements UserRepositoryInterface
{
    public function getLoggedInUser(): UserEntity
    {
        $user = new UserEntity();
        $user->setId(UserRepositoryInterface::GUEST_USER);
        return $user;
    }
}