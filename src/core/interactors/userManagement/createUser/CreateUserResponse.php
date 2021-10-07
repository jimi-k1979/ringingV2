<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\userManagement\createUser;


use DrlArchive\core\classes\Response;

class CreateUserResponse extends Response
{
    public const DATA_USERNAME = 'username';
    public const DATA_EMAIL_ADDRESS = 'emailAddress';
    public const DATA_USER_ID = 'userId';
}
