<?php
declare(strict_types=1);

namespace DrlArchive\implementation\interfaces;

use \PDOStatement;

interface SqlDatabaseInterface
{
    public const SINGLE_ROW = 1;
    public const MULTI_ROW = 0;

    public function query(
        string $sql,
        array $params = [],
        int $queryType = self::MULTI_ROW
    ): array;

    public function execute(string $sql, array $params = []): int;

    public function executeAndFetch(
        string $sql,
        array $params = []
    ): array;

    public function startTransaction(): void;

    public function commitTransaction(): void;

    public function rollbackTransaction(): void;

    public function getLastInsertId(): int;

    public function fetchReadOnlyStatement(
        string $query,
        array $params = []
    ): PDOStatement;

    public function closeStatementCursor(PDOStatement $statement): void;
}