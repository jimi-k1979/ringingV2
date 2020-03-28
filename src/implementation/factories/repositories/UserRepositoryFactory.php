<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories;


use DrlArchive\core\interfaces\factories\repositories\UserRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\repositories\existing\ExistingUserObject;

class UserRepositoryFactory implements UserRepositoryFactoryInterface
{

    public function create(?int $userId = null): UserRepositoryInterface
    {
        $user = new ExistingUserObject();
        if ($userId === null) {
            $userId = UserRepositoryInterface::GUEST_USER;
        }
        $user->setUserId($userId);
        return $user;
    }
}