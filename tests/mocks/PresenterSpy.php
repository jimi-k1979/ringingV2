<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\classes\Response;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;

class PresenterSpy implements PresenterInterface
{

    private ?Response $response;
    private bool $sendCalled = false;

    public function send(?Response $response = null): void
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