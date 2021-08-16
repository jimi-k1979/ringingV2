<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;


class UserEntity extends Entity
{
    public const ADD_NEW_PERMISSION = 'addNew';
    public const EDIT_EXISTING_PERMISSION = 'editExisting';
    public const APPROVE_EDIT_PERMISSION = 'approveEdit';
    public const CONFIRM_DELETE_PERMISSION = 'confirmDelete';

    private ?string $username = null;
    private ?string $emailAddress = null;
    private ?string $password = null;
    /**
     * @var bool[]
     */
    private array $permissions = [
        self::ADD_NEW_PERMISSION => false,
        self::EDIT_EXISTING_PERMISSION => false,
        self::APPROVE_EDIT_PERMISSION => false,
        self::CONFIRM_DELETE_PERMISSION => false,
    ];
    private int $loginCount = 0;

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    /**
     * @param string|null $emailAddress
     */
    public function setEmailAddress(?string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param array $permission
     */
    public function setPermissions(array $permission): void
    {
        $this->permissions = $permission;
    }

    /**
     * @return int
     */
    public function getLoginCount(): int
    {
        return $this->loginCount;
    }

    /**
     * @param int $param
     */
    public function setLoginCount(int $param): void
    {
        $this->loginCount = $param;
    }


}
