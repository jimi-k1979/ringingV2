<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\TestConstants;

trait CreateMockUserTrait
{
    private function createMockSuperAdmin(): UserEntity
    {
        $user = new UserEntity();
        $user->setId(TestConstants::TEST_USER_ID);
        $user->setEmailAddress(TestConstants::TEST_USER_EMAIL);
        $user->setUsername(TestConstants::TEST_USER_USERNAME);
        $user->setPermissions(TestConstants::TEST_USER_SUPER_ADMIN_ROLE);

        return $user;
    }
}
