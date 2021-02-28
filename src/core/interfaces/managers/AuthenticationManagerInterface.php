<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\managers;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;

interface AuthenticationManagerInterface
{

    public const INVALID_USERNAME = 1301;
    public const INVALID_EMAIL_ADDRESS = 1302;
    public const INVALID_PASSWORD = 1303;
    public const USER_EXISTS = 1304;
    public const TOO_MANY_REQUESTS = 1305;
    public const INVALID_CREDENTIALS = 1306;
    public const EMAIL_NOT_VERIFIED = 1307;
    public const FEATURE_DISABLED = 1308;
    public const INVALID_TOKEN = 1309;
    public const TOKEN_EXPIRED = 1310;
    public const NOT_LOGGED_IN = 1311;
    public const UNKNOWN_USER_ID = 1312;

    public static function createManager(): self;

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
