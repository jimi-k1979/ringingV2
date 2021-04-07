<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\managers;


use Delight\Auth\AttemptCancelledException;
use Delight\Auth\Auth;
use Delight\Auth\AuthError;
use Delight\Auth\EmailNotVerifiedException;
use Delight\Auth\InvalidEmailException;
use Delight\Auth\InvalidPasswordException;
use Delight\Auth\InvalidSelectorTokenPairException;
use Delight\Auth\NotLoggedInException;
use Delight\Auth\ResetDisabledException;
use Delight\Auth\Role;
use Delight\Auth\TokenExpiredException;
use Delight\Auth\TooManyRequestsException;
use Delight\Auth\UnknownIdException;
use Delight\Auth\UserAlreadyExistsException;
use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\Exceptions\BadDataException;
use DrlArchive\core\Exceptions\FeatureDisabledException;
use DrlArchive\core\interfaces\managers\AuthenticationManagerInterface;
use DrlArchive\Config;
use PDO;

class AuthenticationManager implements AuthenticationManagerInterface
{
    private Auth $auth;

    /**
     * AuthenticationManager constructor.
     * @param PDO $connection
     */
    private function __construct(PDO $connection)
    {
        $this->auth = new Auth($connection);
    }

    public static function createManager(): self
    {
        return new AuthenticationManager(self::createPDO());
    }

    private static function createPDO(): PDO
    {
        return new PDO(
            'mysql:dbname=' . Config::DB_SCHEMA
            . ';host=' . Config::DB_HOST
            . ';charset=utf8mb4',
            Config::DB_USER,
            Config::DB_PASSWORD
        );
    }

    /**
     * @inheritDoc
     * @throws AuthError
     */
    public function registerUser(UserEntity $userEntity): void
    {
        try {
            if (
                preg_match(
                    '/[\x00-\x1f\x7f\/:\\\\]/',
                    $userEntity->getUsername()
                ) === 0
            ) {
                $userEntity->setId(
                    $this->auth->register(
                        $userEntity->getEmailAddress(),
                        $userEntity->getPassword(),
                        $userEntity->getUsername()
                    )
                );

                $userEntity->setPassword('');
            } else {
                throw new BadDataException(
                    'Username cannot have control characters in it',
                    AuthenticationManagerInterface::INVALID_USERNAME
                );
            }
        } catch (InvalidEmailException $e) {
            throw new BadDataException(
                'Invalid email address',
                AuthenticationManagerInterface::INVALID_EMAIL_ADDRESS
            );
        } catch (InvalidPasswordException $e) {
            throw new BadDataException(
                'Invalid password',
                AuthenticationManagerInterface::INVALID_PASSWORD
            );
        } catch (UserAlreadyExistsException $e) {
            throw new BadDataException(
                'User already exists',
                AuthenticationManagerInterface::USER_EXISTS
            );
        } catch (TooManyRequestsException $e) {
            throw new AccessDeniedException(
                'Too many request',
                AuthenticationManagerInterface::TOO_MANY_REQUESTS
            );
        }
    }

    /**
     * @inheritDoc
     * @throws AttemptCancelledException
     * @throws AuthError
     */
    public function loginUser(UserEntity $userEntity): void
    {
        try {
            $this->auth->login(
                $userEntity->getEmailAddress(),
                $userEntity->getPassword()
            );
            $userEntity->setPassword('');
        } catch (InvalidEmailException | InvalidPasswordException $e) {
            throw new AccessDeniedException(
                'Unknown email or password',
                AuthenticationManagerInterface::INVALID_CREDENTIALS
            );
        } catch (EmailNotVerifiedException $e) {
            throw new AccessDeniedException(
                'Email not verified',
                AuthenticationManagerInterface::EMAIL_NOT_VERIFIED
            );
        } catch (TooManyRequestsException $e) {
            throw new AccessDeniedException(
                'Too many requests',
                AuthenticationManagerInterface::TOO_MANY_REQUESTS
            );
        }
    }

    /**
     * @inheritDoc
     * @throws AuthError
     */
    public function requestPasswordReset(string $emailAddress): array
    {
        try {
            $returnArray = [];
            $this->auth->forgotPassword(
                $emailAddress,
                function ($selector, $token) use (&$returnArray) {
                    $returnArray = [
                        'selector' => $selector,
                        'token' => $token,
                    ];
                }
            );
            return $returnArray;
        } catch (InvalidEmailException $e) {
            throw new AccessDeniedException(
                'Unknown email address',
                AuthenticationManagerInterface::INVALID_EMAIL_ADDRESS
            );
        } catch (EmailNotVerifiedException $e) {
            throw new AccessDeniedException(
                'Email address not verified',
                AuthenticationManagerInterface::EMAIL_NOT_VERIFIED
            );
        } catch (ResetDisabledException $e) {
            throw new FeatureDisabledException(
                'Password reset is disabled',
                AuthenticationManagerInterface::FEATURE_DISABLED
            );
        } catch (TooManyRequestsException $e) {
            throw new AccessDeniedException(
                'Too many requests',
                AuthenticationManagerInterface::TOO_MANY_REQUESTS
            );
        }
    }

    /**
     * @inheritDoc
     * @throws AuthError
     */
    public function verifyResetAttempt(string $selector, string $token): void
    {
        try {
            $this->auth->canResetPasswordOrThrow($selector, $token);
        } catch (InvalidSelectorTokenPairException $e) {
            throw new BadDataException(
                'Invalid token',
                AuthenticationManagerInterface::INVALID_TOKEN
            );
        } catch (TokenExpiredException $e) {
            throw new AccessDeniedException(
                'Token expired',
                AuthenticationManagerInterface::TOKEN_EXPIRED
            );
        } catch (ResetDisabledException $e) {
            throw new FeatureDisabledException(
                'Password reset is disabled',
                AuthenticationManagerInterface::FEATURE_DISABLED
            );
        } catch (TooManyRequestsException $e) {
            throw new AccessDeniedException(
                'Too many requests',
                AuthenticationManagerInterface::TOO_MANY_REQUESTS
            );
        }
    }

    /**
     * @inheritDoc
     * @throws AuthError
     */
    public function completeResetAttempt(
        UserEntity $user,
        string $selector,
        string $token
    ): void {
        try {
            $this->auth->resetPassword($selector, $token, $user->getPassword());
        } catch (InvalidSelectorTokenPairException $e) {
            throw new BadDataException(
                'Invalid Token',
                AuthenticationManagerInterface::INVALID_TOKEN
            );
        } catch (TokenExpiredException $e) {
            throw new AccessDeniedException(
                'Token expired',
                AuthenticationManagerInterface::TOKEN_EXPIRED
            );
        } catch (ResetDisabledException $e) {
            throw new FeatureDisabledException(
                'Password reset is disabled',
                AuthenticationManagerInterface::FEATURE_DISABLED
            );
        } catch (TooManyRequestsException $e) {
            throw new AccessDeniedException(
                'Too many requests',
                AuthenticationManagerInterface::TOO_MANY_REQUESTS
            );
        } catch (InvalidPasswordException $e) {
            throw new BadDataException(
                'Invalid password',
                AuthenticationManagerInterface::INVALID_PASSWORD
            );
        }
    }

    /**
     * @inheritDoc
     * @throws AuthError
     */
    public function changePassword(
        string $oldPassword,
        string $newPassword
    ): void {
        try {
            $this->auth->changePassword($oldPassword, $newPassword);
        } catch (NotLoggedInException $e) {
            throw new AccessDeniedException(
                'Not logged in, password cannot be changed',
                AuthenticationManagerInterface::NOT_LOGGED_IN
            );
        } catch (InvalidPasswordException $e) {
            throw new BadDataException(
                'Invalid password',
                AuthenticationManagerInterface::INVALID_PASSWORD
            );
        } catch (TooManyRequestsException $e) {
            throw new AccessDeniedException(
                'Too many requests',
                AuthenticationManagerInterface::TOO_MANY_REQUESTS
            );
        }
    }

    /**
     * @inheritDoc
     * @throws AuthError
     */
    public function logOutUser(): void
    {
        try {
            $this->auth->logOutEverywhere();
        } catch (NotLoggedInException $e) {
            throw new AccessDeniedException(
                'Not logged in',
                AuthenticationManagerInterface::NOT_LOGGED_IN
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function isLoggedIn(): bool
    {
        return $this->auth->isLoggedIn();
    }

    /**
     * @inheritDoc
     */
    public function loggedInUserDetails(): UserEntity
    {
        $userEntity = new UserEntity();
        $userEntity->setId(
            $this->auth->getUserId()
        );
        $userEntity->setEmailAddress(
            $this->auth->getEmail()
        );
        $userEntity->setUsername(
            $this->auth->getUsername()
        );

        $permissions = [
            UserEntity::ADD_NEW_PERMISSION => $this->auth->hasAnyRole(
                Role::SUPER_ADMIN,
                Role::ADMIN,
                Role::EDITOR,
                Role::AUTHOR
            ),
            UserEntity::EDIT_EXISTING_PERMISSION => $this->auth->hasAnyRole(
                Role::SUPER_ADMIN,
                Role::ADMIN,
                Role::EDITOR
            ),
            UserEntity::APPROVE_EDIT_PERMISSION => $this->auth->hasAnyRole(
                Role::ADMIN,
                Role::SUPER_ADMIN
            ),
            UserEntity::CONFIRM_DELETE_PERMISSION => $this->auth->hasRole(
                Role::SUPER_ADMIN
            ),
        ];

        $userEntity->setPermissions($permissions);
        return $userEntity;
    }


    /**
     * @inheritDoc
     * @throws AuthError
     */
    public function adminCreateUser(UserEntity $userEntity): void
    {
        try {
            if (
                preg_match(
                    '/[\x00-\x1f\x7f\/:\\\\]/',
                    $userEntity->getUsername()
                ) === 0
            ) {
                $userId = $this->auth->admin()->createUser(
                    $userEntity->getEmailAddress(),
                    $userEntity->getPassword(),
                    $userEntity->getUsername()
                );
                $userEntity->setId($userId);
                $userEntity->setPassword('');
            } else {
                throw new BadDataException(
                    'Invalid username',
                    AuthenticationManagerInterface::INVALID_USERNAME
                );
            }
        } catch (InvalidEmailException $e) {
            throw new BadDataException(
                'Invalid email address',
                AuthenticationManagerInterface::INVALID_EMAIL_ADDRESS
            );
        } catch (InvalidPasswordException $e) {
            throw new BadDataException(
                'Invalid password',
                AuthenticationManagerInterface::INVALID_PASSWORD
            );
        } catch (UserAlreadyExistsException $e) {
            throw new BadDataException(
                'User already exists',
                AuthenticationManagerInterface::USER_EXISTS
            );
        }
    }

    /**
     * @inheritDoc
     * @throws AuthError
     */
    public function adminDeleteUser(UserEntity $userEntity): void
    {
        try {
            $this->auth->admin()->deleteUserById($userEntity->getId());
        } catch (UnknownIdException $e) {
            throw new BadDataException(
                'Unknown user id',
                AuthenticationManagerInterface::UNKNOWN_USER_ID
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function adminAssignRoles(UserEntity $userEntity): void
    {
        try {
            if ($userEntity->getPermissions()[UserEntity::ADD_NEW_PERMISSION]) {
                $this->auth->admin()->addRoleForUserById(
                    $userEntity->getId(),
                    Role::AUTHOR
                );
            }

            if ($userEntity->getPermissions()[UserEntity::EDIT_EXISTING_PERMISSION]) {
                $this->auth->admin()->addRoleForUserById(
                    $userEntity->getId(),
                    Role::EDITOR
                );
            }

            if ($userEntity->getPermissions()[UserEntity::APPROVE_EDIT_PERMISSION]) {
                $this->auth->admin()->addRoleForUserById(
                    $userEntity->getId(),
                    Role::ADMIN
                );
            }

            if ($userEntity->getPermissions()[UserEntity::CONFIRM_DELETE_PERMISSION]) {
                $this->auth->admin()->addRoleForUserById(
                    $userEntity->getId(),
                    Role::SUPER_ADMIN
                );
            }
        } catch (UnknownIdException $e) {
            throw new BadDataException(
                'Unknown user id',
                AuthenticationManagerInterface::UNKNOWN_USER_ID
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function adminRemoveRoles(UserEntity $userEntity): void
    {
        try {
            if (!($userEntity->getPermissions()[UserEntity::ADD_NEW_PERMISSION])) {
                $this->auth->admin()->removeRoleForUserById(
                    $userEntity->getId(),
                    Role::AUTHOR
                );
            }

            if (!($userEntity->getPermissions()[UserEntity::EDIT_EXISTING_PERMISSION])) {
                $this->auth->admin()->removeRoleForUserById(
                    $userEntity->getId(),
                    Role::EDITOR
                );
            }

            if (!($userEntity->getPermissions()[UserEntity::APPROVE_EDIT_PERMISSION])) {
                $this->auth->admin()->removeRoleForUserById(
                    $userEntity->getId(),
                    Role::ADMIN
                );
            }

            if (!($userEntity->getPermissions()[UserEntity::CONFIRM_DELETE_PERMISSION])) {
                $this->auth->admin()->removeRoleForUserById(
                    $userEntity->getId(),
                    Role::SUPER_ADMIN
                );
            }
        } catch (UnknownIdException $e) {
            throw new BadDataException(
                'Unknown user id',
                AuthenticationManagerInterface::UNKNOWN_USER_ID
            );
        }
    }
}
