<?php

declare(strict_types=1);

namespace DrlArchive;

/**
 * Class Config
 * File that sets the environmental configuration
 */
class ConfigExample
{
    public const HOST_NAME = 'applicationHostServer';

    public const DB_HOST = 'databaseHost';
    public const DB_SCHEMA = 'databaseSchema';
    public const DB_USER = 'databaseUser';
    public const DB_PASSWORD = 'databasePassword';

    public const SMTP_HOST_SERVER = 'smtpServer';
    public const SMTP_REQUIRES_AUTHENTICATION = true;
    public const SMTP_ENCRYPTION_PROTOCOL = 'smtpEncryptionProtocol';
    public const SMTP_USERNAME = 'smtpUser';
    public const SMTP_PASSWORD = 'smtpPassword';
    public const SMTP_PORT = 587;

}
