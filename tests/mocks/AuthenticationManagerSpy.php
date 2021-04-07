<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interfaces\managers\AuthenticationManagerInterface;

class AuthenticationManagerSpy implements AuthenticationManagerInterface
{

    private bool $registerUserCalled = false;
    private bool $registerUserThrowsException = false;
    private bool $loginUserCalled = false;
    private bool $loginUserThrowsException = false;
    private bool $requestPasswordResetCalled = false;
    private bool $requestPasswordResetThrowsException = false;
    private array $requestPasswordResetValue = [];
    private bool $verifyResetAttemptCalled = false;
    private bool $verifyResetAttemptThrowsException = false;
    private bool $completeResetAttemptCalled = false;
    private bool $completeResetAttemptThrowsException = false;
    private bool $changePasswordCalled = false;
    private bool $changePasswordThrowsException = false;
    private bool $logOutUserCalled = false;
    private bool $logOutUserThrowsException = false;
    private bool $isLoggedInCalled = false;
    private bool $isLoggedInValue = true;
    private bool $loggedInUserDetailsCalled = false;
    private UserEntity $loggedInUserDetailsValue;
    private bool $adminCreateUserCalled = false;
    private bool $adminCreateUserThrowsException = false;
    private bool $adminDeleteUserCalled = false;
    private bool $adminDeleteUserThrowsException = false;
    private bool $adminAssignRolesCalled = false;
    private bool $adminAssignRolesThrowsException = false;
    private bool $adminRemoveRolesCalled = false;
    private bool $adminRemoveRolesThrowsException = false;

    /**
     * @inheritDoc
     */
    public function registerUser(UserEntity $userEntity): void
    {
        $this->registerUserCalled = true;
        if ($this->registerUserThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                AuthenticationManagerInterface::INVALID_USERNAME
            );
        }
    }

    public function hasRegisterUserBeenCalled(): bool
    {
        return $this->registerUserCalled;
    }

    public function setRegisterUserThrowsException(): void
    {
        $this->registerUserThrowsException = true;
    }

    /**
     * @inheritDoc
     */
    public function loginUser(UserEntity $userEntity): void
    {
        $this->loginUserCalled = true;
        if ($this->loginUserThrowsException) {
            throw new AccessDeniedException(
                'Something went wrong',
                AuthenticationManagerInterface::NOT_LOGGED_IN
            );
        }
    }

    public function hasLoginUserBeenCalled(): bool
    {
        return $this->loginUserCalled;
    }

    public function setLoginUserThrowsException(): void
    {
        $this->loginUserThrowsException = true;
    }

    /**
     * @inheritDoc
     */
    public function requestPasswordReset(string $emailAddress): array
    {
        $this->requestPasswordResetCalled = true;
        if ($this->requestPasswordResetThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                AuthenticationManagerInterface::INVALID_PASSWORD
            );
        }

        return $this->requestPasswordResetValue;
    }

    public function hasRequestPasswordResetBeenCalled(): bool
    {
        return $this->requestPasswordResetCalled;
    }

    public function setRequestPasswordResetThrowsException(): void
    {
        $this->requestPasswordResetThrowsException = true;
    }

    public function setRequestPasswordResetValue(array $value): void
    {
        $this->requestPasswordResetValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function verifyResetAttempt(string $selector, string $token): void
    {
        $this->verifyResetAttemptCalled = true;
        if ($this->verifyResetAttemptThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                AuthenticationManagerInterface::INVALID_TOKEN
            );
        }
    }

    public function hasVerifyResetAttemptBeenCalled(): bool
    {
        return $this->verifyResetAttemptCalled;
    }

    public function setVerifyResetAttemptThrowsException(): void
    {
        $this->verifyResetAttemptThrowsException = true;
    }

    /**
     * @inheritDoc
     */
    public function completeResetAttempt(
        UserEntity $user,
        string $selector,
        string $token
    ): void {
        $this->completeResetAttemptCalled = true;
        if ($this->completeResetAttemptThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                AuthenticationManagerInterface::INVALID_TOKEN
            );
        }
    }

    public function hasCompleteResetAttemptBeenCalled(): bool
    {
        return $this->completeResetAttemptCalled;
    }

    public function setCompleteResetAttemptThrowsException(): void
    {
        $this->completeResetAttemptThrowsException = true;
    }

    /**
     * @inheritDoc
     */
    public function changePassword(
        string $oldPassword,
        string $newPassword
    ): void {
        $this->changePasswordCalled = true;
        if ($this->changePasswordThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                AuthenticationManagerInterface::INVALID_PASSWORD
            );
        }
    }

    public function hasChangePasswordBeenCalled(): bool
    {
        return $this->changePasswordCalled;
    }

    public function setChangePasswordThrowsException(): void
    {
        $this->changePasswordThrowsException = true;
    }

    /**
     * @inheritDoc
     */
    public function logOutUser(): void
    {
        $this->logOutUserCalled = true;
        if ($this->logOutUserThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                AuthenticationManagerInterface::TOO_MANY_REQUESTS
            );
        }
    }

    public function hasLogOutUserBeenCalled(): bool
    {
        return $this->logOutUserCalled;
    }

    public function setLogOutUserThrowsException(): void
    {
        $this->logOutUserThrowsException = true;
    }

    /**
     * @inheritDoc
     */
    public function isLoggedIn(): bool
    {
        $this->isLoggedInCalled = true;
        return $this->isLoggedInValue;
    }

    public function hasIsLoggedInBeenCalled(): bool
    {
        return $this->isLoggedInCalled;
    }

    public function setIsLoggedInToFalse(): void
    {
        $this->isLoggedInValue = false;
    }

    /**
     * @inheritDoc
     */
    public function loggedInUserDetails(): UserEntity
    {
        $this->loggedInUserDetailsCalled = true;
        return $this->loggedInUserDetailsValue ?? new UserEntity();
    }

    public function hasLoggedInUserDetailsBeenCalled(): bool
    {
        return $this->loggedInUserDetailsCalled;
    }

    public function setLoggedInUserDetailsValue(UserEntity $value): void
    {
        $this->loggedInUserDetailsValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function adminCreateUser(UserEntity $userEntity): void
    {
        $this->adminCreateUserCalled = true;
        if ($this->adminCreateUserThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                AuthenticationManagerInterface::UNKNOWN_USER_ID
            );
        }
    }

    public function hasAdminCreateUserBeenCalled(): bool
    {
        return $this->adminCreateUserCalled;
    }

    public function setAdminCreateUserThrowsException(): void
    {
        $this->adminCreateUserThrowsException = true;
    }

    /**
     * @inheritDoc
     */
    public function adminDeleteUser(UserEntity $userEntity): void
    {
        $this->adminDeleteUserCalled = true;
        if ($this->adminDeleteUserThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                AuthenticationManagerInterface::UNKNOWN_USER_ID
            );
        }
    }

    public function hasAdminDeleteUserBeenCalled(): bool
    {
        return $this->adminDeleteUserCalled;
    }

    public function setAdminDeleteUserThrowsException(): void
    {
        $this->adminDeleteUserThrowsException = true;
    }

    /**
     * @inheritDoc
     */
    public function adminAssignRoles(UserEntity $userEntity): void
    {
        $this->adminAssignRolesCalled = true;
        if ($this->adminAssignRolesThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                AuthenticationManagerInterface::UNKNOWN_USER_ID
            );
        }
    }

    public function hasAdminAssignRolesBeenCalled(): bool
    {
        return $this->adminAssignRolesCalled;
    }

    public function setAdminAssignRolesThrowsException(): void
    {
        $this->adminAssignRolesThrowsException = true;
    }

    /**
     * @inheritDoc
     */
    public function adminRemoveRoles(UserEntity $userEntity): void
    {
        $this->adminRemoveRolesCalled = true;
        if ($this->adminRemoveRolesThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                AuthenticationManagerInterface::UNKNOWN_USER_ID
            );
        }
    }

    public function hasAdminRemoveRolesBeenCalled(): bool
    {
        return $this->adminRemoveRolesCalled;
    }

    public function setAdminRemoveRolesThrowsException(): void
    {
        $this->adminRemoveRolesThrowsException = true;
    }
}
