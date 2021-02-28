<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;


class UserEntity extends Entity
{
    public const ADD_NEW_PERMISSION = 'addNew';
    public const EDIT_EXISTING_PERMISSION = 'editExisting';
    public const APPROVE_EDIT_PERMISSION = 'approveEdit';
    public const CONFIRM_DELETE_PERMISSION = 'confirmDelete';

    private string $username;
    private ?string $emailAddress;
    private ?string $password;
    /**
     * @var bool[]
     */
    private array $permissions = [
        'addNew' => false,
        'editExisting' => false,
        'approveEdit' => false,
        'delete' => false,
    ];

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress(string $emailAddress): void
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


}
