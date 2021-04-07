<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\userManagement\loginUser;


use DrlArchive\core\classes\Request;
use DrlArchive\core\Constants;

class LoginUserRequest extends Request
{
    public const EMAIL_ADDRESS = 'emailAddress';
    public const USERNAME = 'username';
    public const PASSWORD = 'password';
    public const REDIRECT_TO = 'redirectTo';

    protected array $schema = [
        self::EMAIL_ADDRESS => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_EMAIL,
            parent::OPTION_REQUIRED => false,
            parent::OPTION_ALLOW_NULL => true,
        ],
        self::USERNAME => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => false,
            parent::OPTION_ALLOW_NULL => true,
        ],
        self::PASSWORD => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => false,
            parent::OPTION_ALLOW_NULL => true,
        ],
        self::REDIRECT_TO => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => Constants::INDEX_PAGE_ADDRESS,
        ],
    ];

    public function getEmailAddress(): ?string
    {
        return $this->data[self::EMAIL_ADDRESS];
    }

    public function setEmailAddress(?string $input): void
    {
        $this->updateModel(self::EMAIL_ADDRESS, $input);
    }

    public function getUsername(): ?string
    {
        return $this->data[self::USERNAME];
    }

    public function setUsername(?string $input): void
    {
        $this->updateModel(self::USERNAME, $input);
    }

    public function getPassword(): ?string
    {
        return $this->data[self::PASSWORD];
    }

    public function setPassword(?string $input): void
    {
        $this->updateModel(self::PASSWORD, $input);
    }

    public function getRedirectTo(): string
    {
        return $this->data[self::REDIRECT_TO];
    }

    public function setRedirectTo(string $input): void
    {
        $this->updateModel(self::REDIRECT_TO, $input);
    }

}
