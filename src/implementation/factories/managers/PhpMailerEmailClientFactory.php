<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\managers;


use DrlArchive\core\interfaces\factories\managers\EmailClientFactoryInterface;
use DrlArchive\core\interfaces\managers\EmailClientInterface;
use DrlArchive\implementation\repositories\managers\PhpMailerEmailClient;

class PhpMailerEmailClientFactory implements EmailClientFactoryInterface
{

    public function create(): EmailClientInterface
    {
        return new PhpMailerEmailClient();
    }
}
