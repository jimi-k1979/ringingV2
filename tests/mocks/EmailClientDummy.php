<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\EmailMessageEntity;
use DrlArchive\core\interfaces\managers\EmailClientInterface;

class EmailClientDummy implements EmailClientInterface
{

    public function sendMessage(EmailMessageEntity $message): void
    {
    }
}
