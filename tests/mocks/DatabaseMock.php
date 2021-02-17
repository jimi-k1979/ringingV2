<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\implementation\interfaces\SqlDatabaseInterface;
use InvalidArgumentException;
use PDOStatement;

class DatabaseMock implements SqlDatabaseInterface
{

    public const FIRST_CALL = 0;
    public const SECOND_CALL = 1;
    public const THIRD_CALL = 2;
    public const FORTH_CALL = 3;
    public const FIFTH_CALL = 4;
    public const SIXTH_CALL = 5;
    public const SEVENTH_CALL = 6;
    public const EIGHTH_CALL = 7;
    public const NINTH_CALL = 8;
    public const TENTH_CALL = 9;

    private int $queryCount = 0;
    private int $startTransactionCount = 0;
    private int $commitTransactionCount = 0;
    private int $rollbackTransactionCount = 0;
    private int $executeCount = 0;
    private int $executeAndFetchCount = 0;

    private array $queryResults = [];

    private bool $startTransactionCalled = false;
    private bool $commitTransactionCalled = false;
    private bool $rollbackTransactionCalled = false;
    private bool $executeCalled = false;
    private bool $executeAndFetchCalled = false;
    private bool $queryCalled = false;

    private array $queryArgs;
    private array $executeArgs;


    public function __construct()
    {
        $this->queryArgs = [
            'sql' => '',
            'placeholders' => [],
        ];

        $this->executeArgs = [
            'sql' => '',
            'placeholders' => [],
        ];
    }

    public function query(
        string $sql,
        array $params = [],
        int $queryType = SqlDatabaseInterface::FETCH_MULTI_ROW
    ) {
        $this->queryCalled = true;
        $this->queryArgs[$this->queryCount] = [
            'sql' => $sql,
            'placeholders' => $params
        ];

        if (!isset($this->queryResults[$this->queryCount])) {
            throw new  InvalidArgumentException(
                'No mocked test results to return'
            );
        }

        return $this->queryResults[$this->queryCount++];
    }

    public function execute(string $sql, array $params = []): int
    {
        $this->executeArgs[$this->executeCount] = [
            'sql' => $sql,
            'placeholders' => $params,
        ];

        $this->executeCalled = true;
        $this->executeCount++;

        return 1;
    }

    public function executeAndFetch(
        string $sql,
        array $params = []
    ): array {
        $this->executeArgs[$this->executeCount] = [
            'sql' => $sql,
            'placeholders' => $params,
        ];

        $this->executeAndFetchCalled = true;
        $this->executeAndFetchCount++;
        return $this->queryResults[$this->queryCount++];
    }


    public function startTransaction(): void
    {
        $this->startTransactionCount++;
        $this->startTransactionCalled = true;
    }


    public function commitTransaction(): void
    {
        $this->commitTransactionCount++;
        $this->commitTransactionCalled = true;
    }


    public function rollbackTransaction(): void
    {
        $this->rollbackTransactionCount++;
        $this->rollbackTransactionCalled = true;
    }

    public function getQueryCount(): int
    {
        return $this->queryCount;
    }


    public function getStartTransactionCount(): int
    {
        return $this->startTransactionCount;
    }


    public function getCommitTransactionCount(): int
    {
        return $this->commitTransactionCount;
    }

    public function getRollbackTransactionCount(): int
    {
        return $this->rollbackTransactionCount;
    }


    public function hasStartTransactionBeenCalled(): bool
    {
        return $this->startTransactionCalled;
    }


    public function hasCommitTransactionBeenCalled(): bool
    {
        return $this->commitTransactionCalled;
    }


    public function isRollbackTransactionCalled(): bool
    {
        return $this->rollbackTransactionCalled;
    }


    public function getExecuteCount(): int
    {
        return $this->executeCount;
    }


    public function hasExecuteBeenCalled(): bool
    {
        return $this->executeCalled;
    }

    public function getExecuteAndFetchCount(): int
    {
        return $this->executeAndFetchCount;
    }


    public function hasExecuteAndFetchBeenCalled(): bool
    {
        return $this->executeAndFetchCalled;
    }


    public function setQueryResults(array $queryResults): void
    {
        $this->queryResults = $queryResults;
    }


    public function addQueryResult(int $count, array $results): void
    {
        if (isset($this->queryResults[$count])) {
            throw new InvalidArgumentException(
                'Return value already exists at position ' . $count
            );
        }

        $this->queryResults[$count] = $results;
    }

    public function getQueryArgs(): array
    {
        return $this->queryArgs;
    }


    public function getExecuteArgs(): array
    {
        return $this->executeArgs;
    }


    public function getLastInsertId(): string
    {
        return (string)rand(1, 1000);
    }


    public function hasQueryBeenCalled(): bool
    {
        return $this->queryCalled;
    }

    public function fetchReadOnlyStatement(
        string $query,
        array $params = []
    ): PDOStatement {
        return new PDOStatement();
    }

    public function closeStatementCursor(PDOStatement $oStatement): void
    {
        $oStatement->closeCursor();
    }


    public function getRowCount(): int
    {
        return 0;
    }

}