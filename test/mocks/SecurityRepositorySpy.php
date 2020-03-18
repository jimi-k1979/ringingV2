<?php

declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;

class SecurityRepositorySpy implements SecurityRepositoryInterface
{
    private $hasIsUserAuthorisedCalled = false;
    private $isAuthorisedResponse = true;

    public function isUserAuthorised(
        UserEntity $user,
        ?string $permission = null
    ): bool {
        $this->hasIsUserAuthorisedCalled = true;
        if ($permission === null) {
            return true;
        }
        if (!$user->getPermissions()[$permission]) {
            return false;
        }
        return $this->isAuthorisedResponse;
    }

    public function hasIsUserAuthorisedCalled(): bool
    {
        return $this->hasIsUserAuthorisedCalled;
    }


    public function setIsAuthorisedResponseToFalse(): void
    {
        $this->isAuthorisedResponse = false;
    }
}