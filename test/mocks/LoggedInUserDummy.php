<?php

declare(strict_types=1);

namespace test\mocks;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;

class LoggedInUserDummy implements UserRepositoryInterface
{

    public function getLoggedInUser(): UserEntity
    {
        $user = new UserEntity();
        $user->setId(3333);
        $user->setEmailAddress('user@example.com');
        $user->setUsername('testUser');
        $user->setPermissions(
            [
                'addNew' => true,
                'editExisting' => true,
                'approveEdit' => true,
                'delete' => true,
            ]
        );

        return $user;
    }
}