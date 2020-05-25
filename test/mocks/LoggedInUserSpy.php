<?php

declare(strict_types=1);

namespace test\mocks;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;

class LoggedInUserSpy implements UserRepositoryInterface
{

    /**
     * @var UserEntity
     */
    private $user;

    public function getLoggedInUser(): UserEntity
    {
        return $this->user;
    }

    public function setUser(UserEntity $userEntity): void
    {
        $this->user = $userEntity;
    }
}