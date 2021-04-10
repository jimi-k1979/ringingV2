<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\managers;


use DrlArchive\core\entities\EmailMessageEntity;
use DrlArchive\core\interfaces\managers\EmailClientInterface;
use PHPMailer\PHPMailer\PHPMailer;

class PhpMailerEmailClient implements EmailClientInterface
{
    // constructor option fields
    public const IS_SMTP = 'isSmtp';
    public const SMTP_HOST = 'host';
    public const SMTP_REQUIRES_AUTH = 'auth';
    public const SMTP_USERNAME = 'username';
    public const SMTP_PASSWORD = 'password';
    public const SMTP_ENCRYPT_PROTOCOL = 'protocol';
    public const SMTP_PORT = 'port';

    private PHPMailer $mailer;

    private function __construct()
    {
        $this->mailer = new PHPMailer(true);
    }

    public static function createClient(array $options = []): self
    {
        $client = new self();
        if (!empty($options)) {
            self::applyOptions($options, $client);
        }
        return $client;
    }

    /**
     * @param array $options
     * @param PhpMailerEmailClient $client
     */
    private static function applyOptions(
        array $options,
        PhpMailerEmailClient $client
    ): void {
        if ($options[self::IS_SMTP]) {
            $client->mailer->isSMTP();
            $client->mailer->Host = $options[self::SMTP_HOST];
            $client->mailer->SMTPSecure = $options[self::SMTP_ENCRYPT_PROTOCOL];
            $client->mailer->Port = $options[self::SMTP_PORT];
            $client->mailer->SMTPAuth = $options[self::SMTP_REQUIRES_AUTH];
            if ($options[self::SMTP_REQUIRES_AUTH]) {
                $client->mailer->Username = $options[self::SMTP_USERNAME];
                $client->mailer->Password = $options[self::SMTP_PASSWORD];
            }
        }
    }

    public function sendMessage(EmailMessageEntity $message): void
    {
        $this->mailer->setFrom($message->getFromAddress());
        foreach ($message->getRecipientAddresses() as $recipientAddress) {
            $this->mailer->addAddress($recipientAddress);
        }
        $this->mailer->Subject = $message->getSubject();
        $this->mailer->Body = $message->getMessageBody();
        $this->mailer->send();
    }
}
