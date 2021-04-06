<?php

declare(strict_types=1);

namespace DrlArchive;


use Config;

class Settings
{
    public const DB_HOST = Config::DB_HOST;
    public const DB_SCHEMA = Config::DB_SCHEMA;
    public const DB_USER = Config::DB_USER;
    public const DB_PASSWORD = Config::DB_PASSWORD;

    public const HOST_NAME = Config::HOST_NAME;

    // --Commented out by Inspection (17/02/2021 20:18):public const PUBLIC_FOLDER = __DIR__ . '/../html/';

}
