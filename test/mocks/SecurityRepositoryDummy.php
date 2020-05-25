<?php

declare(strict_types=1);

namespace test\mocks;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;

class SecurityRepositoryDummy implements SecurityRepositoryInterface
{

    public function isUserAuthorised(
        UserEntity $user,
        ?string $permission = null
    ): bool {
        return true;
    }
}