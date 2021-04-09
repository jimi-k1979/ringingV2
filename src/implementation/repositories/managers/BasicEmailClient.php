<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\managers;


use DrlArchive\core\entities\EmailMessageEntity;
use DrlArchive\core\interfaces\managers\EmailClientInterface;

class BasicEmailClient implements EmailClientInterface
{

    public function sendMessage(EmailMessageEntity $message): void
    {
        foreach ($message->getRecipientAddresses() as $recipientAddress) {
            mail(
                $recipientAddress,
                $message->getSubject(),
                $message->getMessageBody(),
                $message->getHeaders()
            );
        }
    }
}
