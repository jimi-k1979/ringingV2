<?php
declare(strict_types=1);

namespace DrlArchive\implementation\interfaces;

interface SqlDatabaseInterface
{
    public const FETCH_SINGLE_ROW = 1;
    public const FETCH_MULTI_ROW = 0;
    public const FETCH_SINGLE_VALUE = 2;

    public function query(
        string $sql,
        array $params = [],
        int $queryType = self::FETCH_MULTI_ROW
    );

    public function execute(string $sql, array $params = []): int;

    public function executeAndFetch(
        string $sql,
        array $params = []
    ): array;

    public function startTransaction(): void;

    public function commitTransaction(): void;

    public function rollbackTransaction(): void;

    public function getLastInsertId(): string;

}
