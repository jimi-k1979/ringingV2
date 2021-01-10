<?php
declare(strict_types=1);

namespace DrlArchive\core\classes;


class Response
{
    public const STATUS_SUCCESS = 0;
    public const STATUS_DUPLICATE = 1;
    public const STATUS_NOT_FOUND = 404;
    public const STATUS_NOT_CREATED = 3;
    public const STATUS_NOT_UPDATED = 4;
    public const STATUS_NOT_DELETED = 5;
    public const STATUS_UNKNOWN_ERROR = 500;

    public const RESPONSE_STATUS = 'status';
    public const RESPONSE_MESSAGE = 'message';
    public const RESPONSE_DATA = 'data';

    private $status = self::STATUS_SUCCESS;

    private $message = '';

    private $data = [];


    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->processData($data);
        }
    }


    public function getStatus(): int
    {
        return $this->status;
    }


    public function getMessage(): string
    {
        return $this->message;
    }


    public function getData(): array
    {
        return $this->data;
    }


    private function processData(array $data): void
    {
        $this->status = $data[self::RESPONSE_STATUS] ?? self::STATUS_SUCCESS;
        $this->message = $data[self::RESPONSE_MESSAGE] ?? '';
        $this->data = $data[self::RESPONSE_DATA] ?? [];
    }


    public function setStatus(int $status): void
    {
        $this->status = $status;
    }


    public function setMessage(string $message): void
    {
        $this->message = $message;
    }


    public function setData(array $data): void
    {
        $this->data = $data;
    }
}