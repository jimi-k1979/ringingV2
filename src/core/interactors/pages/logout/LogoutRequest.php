<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\logout;


use DrlArchive\core\classes\Request;

class LogoutRequest extends Request
{
    public const FORWARD_TO = 'forwardTo';

    protected array $schema = [
        self::FORWARD_TO => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => false,
            parent::OPTION_ALLOW_NULL => true,
            parent::OPTION_DEFAULT => '/index.php',
        ],
    ];

    public function getForwardTo(): string
    {
        return $this->data[self::FORWARD_TO];
    }

    public function setForwardTo(string $input): void
    {
        $this->updateModel(self::FORWARD_TO, $input);
    }

}
