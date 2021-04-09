<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\userManagement\logoutUser;


use DrlArchive\core\classes\Request;

class LogoutUserRequest extends Request
{
    public const REDIRECT_TO = 'redirectTo';

    protected array $schema = [
        self::REDIRECT_TO => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => '/index.php',
        ],
    ];

    public function getRedirectTo(): string
    {
        return $this->data[self::REDIRECT_TO];
    }

    public function setRedirectTo(string $input): void
    {
        $this->updateModel(self::REDIRECT_TO, $input);
    }

}
