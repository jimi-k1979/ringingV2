<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\forgottenPassword;


use DrlArchive\core\classes\Request;

class ForgottenPasswordRequest extends Request
{
    public const EMAIL_ADDRESS = 'emailAddress';
    public const RESET_SELECTOR = 'selector';
    public const RESET_TOKEN = 'token';
    public const NEW_PASSWORD = 'newPassword';

    protected array $schema = [
        self::EMAIL_ADDRESS => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_EMAIL,
            parent::OPTION_REQUIRED => false,
            parent::OPTION_ALLOW_NULL => true,
            parent::OPTION_DEFAULT => null,
        ],
        self::RESET_SELECTOR => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => false,
            parent::OPTION_ALLOW_NULL => true,
            parent::OPTION_DEFAULT => null,
        ],
        self::RESET_TOKEN => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => false,
            parent::OPTION_ALLOW_NULL => true,
            parent::OPTION_DEFAULT => null,
        ],
        self::NEW_PASSWORD => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => false,
            parent::OPTION_ALLOW_NULL => true,
            parent::OPTION_DEFAULT => null,
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

    public function getSelector(): ?string
    {
        return $this->data[self::RESET_SELECTOR];
    }

    public function setSelector(?string $input): void
    {
        $this->updateModel(self::RESET_SELECTOR, $input);
    }

    public function getToken(): ?string
    {
        return $this->data[self::RESET_TOKEN];
    }

    public function setToken(?string $input): void
    {
        $this->updateModel(self::RESET_TOKEN, $input);
    }

    public function getNewPassword(): ?string
    {
        return $this->data[self::NEW_PASSWORD];
    }

    public function setNewPassword(?string $input): void
    {
        $this->updateModel(self::NEW_PASSWORD, $input);
    }

}
