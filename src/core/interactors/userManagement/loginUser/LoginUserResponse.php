<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\userManagement\loginUser;


use DrlArchive\core\classes\Response;

class LoginUserResponse extends Response
{
    public const DATA_REDIRECT_TO = 'redirectTo';
    public const DATA_EMAIL_ADDRESS = 'emailAddress';

}
