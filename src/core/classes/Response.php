<?php
declare(strict_types=1);

namespace DrlArchive\core\classes;


use DrlArchive\core\entities\UserEntity;

class Response
{
    public const STATUS_SUCCESS = 200;
    public const STATUS_LOGGED_OUT = 299;
    public const STATUS_FORBIDDEN = 403;
    public const STATUS_NOT_FOUND = 400;
    public const STATUS_TOO_MANY_REQUESTS = 429;
    public const STATUS_UNKNOWN_ERROR = 500;

    public const STATUS_DUPLICATE = 1;
    public const STATUS_NOT_CREATED = 3;
    public const STATUS_NOT_UPDATED = 4;
    public const STATUS_NOT_DELETED = 5;

    public const RESPONSE_STATUS = 'status';
    public const RESPONSE_MESSAGE = 'message';
    public const RESPONSE_DATA = 'data';
    public const RESPONSE_LOGGED_IN_USER = 'loggedInUser';


    private int $status = self::STATUS_SUCCESS;
    private string $message = '';
    private array $data = [];
    private ?UserEntity $loggedInUser = null;

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

    public function getLoggedInUser(): ?UserEntity
    {
        return $this->loggedInUser;
    }

    private function processData(array $data): void
    {
        $this->status = $data[self::RESPONSE_STATUS] ?? self::STATUS_SUCCESS;
        $this->message = $data[self::RESPONSE_MESSAGE] ?? '';
        $this->data = $data[self::RESPONSE_DATA] ?? [];
        $this->loggedInUser = $data[self::RESPONSE_LOGGED_IN_USER] ?? null;
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

    public function setLoggedInUser(UserEntity $userEntity): void
    {
        $this->loggedInUser = $userEntity;
    }
}
