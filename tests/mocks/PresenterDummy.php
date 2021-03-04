<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\classes\Response;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;

class PresenterDummy implements PresenterInterface
{

    public function send(?Response $response = null): void
    {
    }
}
