<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\forgottenPassword;


use DrlArchive\core\classes\Response;

class ForgottenPasswordResponse extends Response
{
    public const DATA_TEMPLATE = 'template';
    public const DATA_TOKEN = 'token';
    public const DATA_SELECTOR = 'selector';

}
