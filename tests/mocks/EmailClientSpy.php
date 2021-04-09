<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\EmailMessageEntity;
use DrlArchive\core\interfaces\managers\EmailClientInterface;

class EmailClientSpy implements EmailClientInterface
{

    private bool $sendMessageCalled = false;

    public function sendMessage(EmailMessageEntity $message): void
    {
        $this->sendMessageCalled = true;
    }

    public function hasSendMessageBeenCalled(): bool
    {
        return $this->sendMessageCalled;
    }
}
