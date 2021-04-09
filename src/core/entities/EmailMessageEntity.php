<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;


use DrlArchive\Implementation;

class EmailMessageEntity
{
    /**
     * @var string[]
     */
    private array $recipientAddresses = [];
    private string $messageBody;
    private string $subject;
    private array $headers = [
        'From' => Implementation::DEFAULT_EMAIL_FROM_ADDRESS,
    ];

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
        // ensure there is always a 'from' header
        if (!isset($headers['From'])) {
            $headers['From'] = Implementation::DEFAULT_EMAIL_FROM_ADDRESS;
        }
        $this->headers = $headers;
    }

    /**
     * @param string $header
     * @param string $value
     */
    public function addHeader(string $header, string $value)
    {
        $this->headers[$header] = $value;
    }

    /**
     * @return string
     */
    public function getFromAddress(): string
    {
        return $this->headers['From'];
    }

    /**
     * Syntactic sugar for setting the 'from' address
     * @param string $emailAddress
     */
    public function setFromAddress(string $emailAddress): void
    {
        $this->addHeader('From', $emailAddress);
    }
}
