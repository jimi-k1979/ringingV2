<?php

declare(strict_types=1);

require_once(__DIR__ . '/init.php');

use DrlArchive\core\classes\Response;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;

$presenter = new class implements PresenterInterface {
    public function send(?Response $response = null): void
    {
        $_SESSION['previousStatus'] = $response->getStatus();
    }
};
