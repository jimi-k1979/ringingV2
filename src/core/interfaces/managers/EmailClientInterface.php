<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\managers;


use DrlArchive\core\entities\EmailMessageEntity;

interface EmailClientInterface
{
    public function sendMessage(EmailMessageEntity $message): void;
}
