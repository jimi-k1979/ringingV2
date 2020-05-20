<?php
declare(strict_types=1);

namespace test\mocks;


use DrlArchive\core\classes\Response;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;

class PresenterSpy implements PresenterInterface
{

    private $response;
    private $sendCalled = false;

    public function send(?Response $response = null)
    {
        $this->response = $response;
        $this->sendCalled = true;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function hasSendBeenCalled(): bool
    {
        return $this->sendCalled;
    }
}