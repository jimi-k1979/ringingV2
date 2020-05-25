<?php
declare(strict_types=1);

namespace test\mocks;


use DrlArchive\core\classes\Response;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;

class PreseenterDummy implements PresenterInterface
{

    public function send(?Response $response = null)
    {
    }
}