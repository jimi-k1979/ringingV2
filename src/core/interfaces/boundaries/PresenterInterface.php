<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\boundaries;


use DrlArchive\core\classes\Response;

interface PresenterInterface
{
    public function send(?Response $response = null): void;
}
