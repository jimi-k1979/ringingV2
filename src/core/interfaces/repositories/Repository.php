<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


class Repository
{
    public const REPOSITORY_ERROR_ALREADY_EXISTS = 100;
    public const REPOSITORY_ERROR_CONNECTION = 101;
    public const REPOSITORY_ERROR_ACCESS_DENIED = 102;
    public const REPOSITORY_ERROR_WRITE = 103;
    public const REPOSITORY_ERROR_READ = 104;
    public const REPOSITORY_ERROR_NOT_FOUND = 105;
    public const REPOSITORY_ERROR_UNKNOWN = -9999;
}
