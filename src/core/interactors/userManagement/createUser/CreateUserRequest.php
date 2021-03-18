<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\userManagement\createUser;


use DrlArchive\core\classes\Request;

class CreateUserRequest extends Request
{
    public const USERNAME = 'userName';
    public const EMAIL_ADDRESS = 'emailAddress';
    public const PASSWORD = 'password';

    protected array $schema = [
        self::USERNAME => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
        self::EMAIL_ADDRESS => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_EMAIL,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
        self::PASSWORD => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
    ];

    public function getUsername(): string
    {
        return $this->data[self::USERNAME];
    }

    public function setUsername(string $input): void
    {
        $this->updateModel(self::USERNAME, $input);
    }

    public function getEmailAddress(): string
    {
        return $this->data[self::EMAIL_ADDRESS];
    }

    public function setEmailAddress(string $input): void
    {
        $this->updateModel(self::EMAIL_ADDRESS, $input);
    }

    public function getPassword(): string
    {
        return $this->data[self::PASSWORD];
    }

    public function setPassword(string $input): void
    {
        $this->updateModel(self::PASSWORD, $input);
    }

}
