<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;


class EmailMessageEntity
{
    /**
     * @var string[]
     */
    private array $recipientAddresses = [];
    private string $messageBody;
    private string $subject;
    private array $headers = [];

    /**
     * @return string[]
     */
    public function getRecipientAddresses(): array
    {
        return $this->recipientAddresses;
    }

    /**
     * @param string[] $recipientAddresses
     */
    public function setRecipientAddresses(array $recipientAddresses): void
    {
        $this->recipientAddresses = $recipientAddresses;
    }

    public function addRecipientAddress(string $emailAddress): void
    {
        $this->recipientAddresses[] = $emailAddress;
    }

    /**
     * @return string
     */
    public function getMessageBody(): string
    {
        return $this->messageBody;
    }

    /**
     * @param string $messageBody
     */
    public function setMessageBody(string $messageBody): void
    {
        $this->messageBody = $messageBody;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

}
