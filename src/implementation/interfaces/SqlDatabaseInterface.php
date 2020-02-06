<?php
declare(strict_types=1);

namespace DrlArchive\implementation\interfaces;

use \PDOStatement;

interface SqlDatabaseInterface
{
    public function query(string $sql, array $params = []): array;

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