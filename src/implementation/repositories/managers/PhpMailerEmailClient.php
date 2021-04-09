<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\managers;


use DrlArchive\core\entities\EmailMessageEntity;
use DrlArchive\core\interfaces\managers\EmailClientInterface;
use PHPMailer\PHPMailer\PHPMailer;

class PhpMailerEmailClient implements EmailClientInterface
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
    }


    public function sendMessage(EmailMessageEntity $message): void
    {
        $this->mailer->setFrom($message->getFromAddress());
        foreach ($message->getRecipientAddresses() as $recipientAddress) {
            $this->mailer->addAddress($recipientAddress);
        }
        $this->mailer->Subject = $message->getSubject();
        $this->mailer->Body = $message->getMessageBody();
        $this->mailer->AltBody = $message->getMessageBody();
        $this->mailer->send();
    }
}
