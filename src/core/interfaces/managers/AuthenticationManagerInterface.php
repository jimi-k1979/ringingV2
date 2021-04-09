<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\managers;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;

interface AuthenticationManagerInterface
{

    public const INVALID_USERNAME_EXCEPTION = 1301;
    public const INVALID_EMAIL_ADDRESS_EXCEPTION = 1302;
    public const INVALID_PASSWORD_EXCEPTION = 1303;
    public const USER_EXISTS_EXCEPTION = 1304;
    public const TOO_MANY_REQUESTS_EXCEPTION = 1305;
    public const INVALID_CREDENTIALS_EXCEPTION = 1306;
    public const EMAIL_NOT_VERIFIED_EXCEPTION = 1307;
    public const FEATURE_DISABLED_EXCEPTION = 1308;
    public const INVALID_TOKEN_EXCEPTION = 1309;
    public const TOKEN_EXPIRED_EXCEPTION = 1310;
    public const NOT_LOGGED_IN_EXCEPTION = 1311;
    public const UNKNOWN_USER_ID_EXCEPTION = 1312;

    /**
     * @param UserEntity $userEntity
     * @throws CleanArchitectureException
     */
    public function registerUser(UserEntity $userEntity): void;

    /**
     * @param UserEntity $userEntity
     * @throws CleanArchitectureException
     */
    public function loginUser(UserEntity $userEntity): void;

    /**
     * @param string $emailAddress
     * @return array
     * @throws CleanArchitectureException
     */
    public function requestPasswordReset(string $emailAddress): array;

    /**
     * @param string $selector
     * @param string $token
     * @throws CleanArchitectureException
     */
    public function verifyResetAttempt(string $selector, string $token): void;

    /**
     * @param UserEntity $user
     * @param string $selector
     * @param string $token
     * @throws CleanArchitectureException
     */
    public function completeResetAttempt(
        UserEntity $user,
        string $selector,
        string $token
    ): void;

    /**
     * @param string $oldPassword
     * @param string $newPassword
     * @throws CleanArchitectureException
     */
    public function changePassword(
        string $oldPassword,
        string $newPassword
    ): void;

    /**
     * @throws CleanArchitectureException
     */
    public function logOutUser(): void;

    /**
     * @return bool
     */
    public function isLoggedIn(): bool;

    /**
     * @return UserEntity
     */
    public function loggedInUserDetails(): UserEntity;

    /**
     * @param UserEntity $userEntity
     * @throws CleanArchitectureException
     */
    public function adminCreateUser(UserEntity $userEntity): void;

    /**
     * @param UserEntity $userEntity
     * @throws CleanArchitectureException
     */
    public function adminDeleteUser(UserEntity $userEntity): void;

    /**
     * @param UserEntity $userEntity
     * @throws CleanArchitectureException
     */
    public function adminAssignRoles(UserEntity $userEntity): void;

    /**
     * @param UserEntity $userEntity
     * @throws CleanArchitectureException
     */
    public function adminRemoveRoles(UserEntity $userEntity): void;


}
