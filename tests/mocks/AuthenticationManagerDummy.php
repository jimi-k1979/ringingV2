<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\interfaces\managers\AuthenticationManagerInterface;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockUserTrait;

class AuthenticationManagerDummy implements AuthenticationManagerInterface
{
    use CreateMockUserTrait;

    /**
     * @inheritDoc
     */
    public function registerUser(UserEntity $userEntity): void
    {
    }

    /**
     * @inheritDoc
     */
    public function loginUser(UserEntity $userEntity): void
    {
    }

    /**
     * @inheritDoc
     */
    public function requestPasswordReset(string $emailAddress): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function verifyResetAttempt(string $selector, string $token): void
    {
    }

    /**
     * @inheritDoc
     */
    public function completeResetAttempt(
        UserEntity $user,
        string $selector,
        string $token
    ): void {
    }

    /**
     * @inheritDoc
     */
    public function changePassword(
        string $oldPassword,
        string $newPassword
    ): void {
    }

    /**
     * @inheritDoc
     */
    public function logOutUser(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function isLoggedIn(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function loggedInUserDetails(): UserEntity
    {
        return $this->createMockSuperAdmin();
    }

    /**
     * @inheritDoc
     */
    public function adminCreateUser(UserEntity $userEntity): void
    {
        $userEntity->setId(TestConstants::TEST_USER_ID);
    }

    /**
     * @inheritDoc
     */
    public function adminDeleteUser(UserEntity $userEntity): void
    {
    }

    /**
     * @inheritDoc
     */
    public function adminAssignRoles(UserEntity $userEntity): void
    {
    }

    /**
     * @inheritDoc
     */
    public function adminRemoveRoles(UserEntity $userEntity): void
    {
    }
}
