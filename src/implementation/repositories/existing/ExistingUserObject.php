<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\existing;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\repositories\doctrine\UserManagementDoctrineFactory;

class ExistingUserObject
    extends Repository
    implements UserRepositoryInterface
{
    private int $userId;
    private UserEntity $user;

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getLoggedInUser(): UserEntity
    {
        if ($this->userId === null) {
            throw new GeneralRepositoryErrorException(
                'No user id given',
                UserRepositoryInterface::NO_USER_ID_GIVEN_EXCEPTION
            );
        }

        if ($this->user === null) {
            $this->user = new UserEntity();
            if ($this->userId === UserRepositoryInterface::GUEST_USER) {
                $this->user->setId(0);
                $this->user->setUsername('guestUser');
                $this->user->setLoginCount(0);
            } else {
                $repo = (new UserManagementDoctrineFactory())->create();
                $this->user = $repo->fetchById($this->userId);
            }
        }

        return $this->user;
    }
}