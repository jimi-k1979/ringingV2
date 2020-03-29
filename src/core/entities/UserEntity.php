<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;


class UserEntity extends Entity
{

    /**
     * @var string
     */
    private $username;
    /**
     * @var string|null
     */
    private $emailAddress;
    /**
     * @var string|null
     */
    private $password;
    /**
     * @var int|null
     */
    private $loginCount;
    /**
     * @var array
     */
    private $permissions = [
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
     * @return int
     */
    public function getLoginCount(): int
    {
        return $this->loginCount;
    }

    /**
     * @param int $loginCount
     */
    public function setLoginCount(int $loginCount): void
    {
        $this->loginCount = $loginCount;
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