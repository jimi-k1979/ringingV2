<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\managers;


use DrlArchive\Config;
use DrlArchive\core\interfaces\factories\managers\EmailClientFactoryInterface;
use DrlArchive\core\interfaces\managers\EmailClientInterface;
use DrlArchive\Implementation;
use DrlArchive\implementation\repositories\managers\PhpMailerEmailClient;

class PhpMailerEmailClientFactory implements EmailClientFactoryInterface
{

    public function create(): EmailClientInterface
    {
        if (Implementation::USE_SMTP_MAILER) {
            $options = [
                PhpMailerEmailClient::IS_SMTP => true,
                PhpMailerEmailClient::SMTP_HOST => Config::SMTP_HOST_SERVER,
                PhpMailerEmailClient::SMTP_ENCRYPT_PROTOCOL =>
                    Config::SMTP_ENCRYPTION_PROTOCOL,
                PhpMailerEmailClient::SMTP_REQUIRES_AUTH =>
                    Config::SMTP_REQUIRES_AUTHENTICATION,
            ];
            if (Config::SMTP_REQUIRES_AUTHENTICATION) {
                $options[PhpMailerEmailClient::SMTP_USERNAME] =
                    Config::SMTP_USERNAME;
                $options[PhpMailerEmailClient::SMTP_PASSWORD] =
                    Config::SMTP_PASSWORD;
            }
        } else {
            $options = [
                PhpMailerEmailClient::IS_SMTP => false,
            ];
        }

        return PhpMailerEmailClient::createClient($options);
    }
}
