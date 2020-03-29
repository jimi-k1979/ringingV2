<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\existing;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;

class ExistingSecurityRepository
    extends Repository
    implements SecurityRepositoryInterface
{

    public function isUserAuthorised(
        UserEntity $user,
        ?string $permission = null
    ): bool {
        if ($permission === null) {
            return true;
        } else {
            return $user->getPermissions()[$permission];
        }
    }
}