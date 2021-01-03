<?php

declare(strict_types=1);

namespace DrlArchive\implementation\interfaces;


use PDOStatement;

interface RawPdoDatabaseInterface extends SqlDatabaseInterface
{
    public function fetchReadOnlyStatement(
        string $query,
        array $params = []
    ): PDOStatement;

    public function closeStatementCursor(PDOStatement $statement): void;
}
