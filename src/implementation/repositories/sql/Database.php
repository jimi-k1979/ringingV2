<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryAlreadyExistsException;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\implementation\entities\DatabaseQueryBuilder;
use DrlArchive\implementation\interfaces\SqlDatabaseInterface;
use DrlArchive\Settings;
use Exception;
use PDO;
use PDOStatement;

class Database implements SqlDatabaseInterface
{
    public const ERROR_CONSTRAINT = 23000;
    public const ERROR_CONNECTION = 2002;
    public const ERROR_ACCESS_DENIED = 1045;
    public const ERROR_DATABASE_NAME = 1049;
    public const ERROR_NO_TABLE_FOUND = '42S02';
    public const ERROR_NO_CODE = 0;

    public const EXCEPTION_NO_FIELDS_IN_SELECT_QUERY = 1210;
    public const EXCEPTION_NO_TABLES_IN_SELECT_QUERY = 1210;

    public const DB_SUPPORTED_MYSQL = 'mysql';
    public const DB_SUPPORTED_PGSQL = 'pgsql';
    public const DB_SUPPORTED_SQLITE = 'sqlite';

    public const SINGLE_VALUE = 'singleValue';
    public const SINGLE_ROW = 'singleRow';
    public const MULTI_ROW = 'multiRow';


    /**
     * @var Database|null
     */
    private static $database;
    /**
     * @var PDO
     */
    private $connection;
    /**
     * @var int
     */
    private $transactionCount = 0;

    /**
     * Database constructor.
     * @param PDO $connection
     */
    private function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Database
     */
    public static function createConnection(): Database
    {
        if (self::$database === null) {
            self::$database = new Database(self::createPDO());
        }

        return self::$database;
    }

    /**
     * @return PDO
     */
    private static function createPDO(): PDO
    {
        $connection = new PDO(
            'mysql:host=' . Settings::DB_HOST . ';dbname=' . Settings::DB_SCHEMA,
            Settings::DB_USER,
            Settings::DB_PASSWORD
        );
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }

    /**
     * @param string $sql
     * @param array $params
     * @param string $queryType
     * @return array
     */
    public function query(
        string $sql,
        array $params = [],
        string $queryType = self::MULTI_ROW
    ): array {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        if ($queryType === self::SINGLE_VALUE) {
            return $stmt->fetchColumn();
        } elseif ($queryType === self::SINGLE_ROW) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @return int
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryAlreadyExistsException
     * @throws RepositoryConnectionErrorException
     */
    public function execute(string $sql, array $params = []): int
    {
        $rowCount = 0;

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $rowCount = $stmt->rowCount();
        } catch (Exception $e) {
            $this->handleError($e);
        }
        return $rowCount;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryAlreadyExistsException
     * @throws RepositoryConnectionErrorException
     */
    public function executeAndFetch(string $sql, array $params = []): array
    {
        $stmt = $this->connection->prepare($sql);
        try {
            $stmt->execute($params);
        } catch (Exception $e) {
            $this->handleError($e);
        }

        return $stmt->fetchAll();
    }

    public function startTransaction(): void
    {
        if ($this->transactionCount === 0) {
            $this->connection->beginTransaction();
            $this->transactionCount++;
        }
    }

    public function commitTransaction(): void
    {
        if (--$this->transactionCount === 0) {
            $this->connection->commit();
        }
    }

    public function rollbackTransaction(): void
    {
        if (--$this->transactionCount === 0) {
            $this->connection->rollBack();
        }
    }

    /**
     * @return int
     */
    public function getLastInsertId(): int
    {
        return (int)$this->connection->lastInsertId();
    }

    /**
     * @param string $query
     * @param array $params
     * @return PDOStatement
     */
    public function fetchReadOnlyStatement(
        string $query,
        array $params = []
    ): PDOStatement {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * @param PDOStatement $statement
     */
    public function closeStatementCursor(PDOStatement $statement): void
    {
        $statement->closeCursor();
    }

    /**
     * @param Exception $e
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryAlreadyExistsException
     * @throws RepositoryConnectionErrorException
     * @throws Exception
     */
    private function handleError(Exception $e): void
    {
        switch ($e->getCode()) {
            case self::ERROR_CONSTRAINT:
                if ($this->recordAlreadyExists($e)) {
                    throw new RepositoryAlreadyExistsException(
                        'The value already exists.',
                        Repository::REPOSITORY_ERROR_ALREADY_EXISTS
                    );
                }
                throw new GeneralRepositoryErrorException($e->getMessage());
                break;

            case self::ERROR_NO_TABLE_FOUND:
                throw new GeneralRepositoryErrorException($e->getMessage());
                break;

            case self::ERROR_CONNECTION:
            case self::ERROR_DATABASE_NAME:
                throw new RepositoryConnectionErrorException(
                    'Error connection to storage',
                    Repository::REPOSITORY_ERROR_CONNECTION
                );
                break;

            case self::ERROR_ACCESS_DENIED:
                throw new RepositoryConnectionErrorException(
                    'Access denied when connecting to storage',
                    Repository::REPOSITORY_ERROR_ACCESS_DENIED
                );
                break;

            case self::ERROR_NO_CODE:
                throw new GeneralRepositoryErrorException(
                    'Storage error',
                    Repository::REPOSITORY_ERROR_UNKNOWN
                );

            default:
                throw $e;
        }
    }

    /**
     * @param Exception $e
     * @return bool
     */
    private function recordAlreadyExists(Exception $e): bool
    {
        return strpos(
                $e->getMessage(),
                'would lead to a duplicate entry'
            ) !== false ||
            strpos(
                $e->getMessage(),
                'is not unique'
            ) !== false ||
            strpos(
                $e->getMessage(),
                'Duplicate entry'
            ) !== false ||
            strpos(
                $e->getMessage(),
                'UNIQUE constraint failed:'
            ) !== false;
    }

    public function buildSelectQuery(
        DatabaseQueryBuilder $query
    ): string {
        if (empty($query->getFields())) {
            throw new GeneralRepositoryErrorException(
                'No fields given in the select query',
                self::EXCEPTION_NO_FIELDS_IN_SELECT_QUERY
            );
        } elseif (empty($query->getTablesAndJoins())) {
            throw new GeneralRepositoryErrorException(
                'No tables given in the select query',
                self::EXCEPTION_NO_TABLES_IN_SELECT_QUERY
            );
        }

        if ($query->isDistinctQuery()) {
            $sql = [
                'SELECT DISTINCT',
                implode(', ', $query->getFields()),
                'FROM',
                implode("\n", $query->getTablesAndJoins()),
            ];
        } else {
            $sql = [
                'SELECT',
                implode(', ', $query->getFields()),
                'FROM',
                implode("\n", $query->getTablesAndJoins()),
            ];
        }

        if (!empty($query->getWhereClauses())) {
            $sql[] = 'WHERE ' . implode(' AND ', $query->getWhereClauses());
        }

        if (!empty($query->getGroupBy())) {
            $sql[] = 'GROUP BY ' . implode(', ', $query->getGroupBy());
        }

        if (!empty($query->getOrderBy())) {
            $sql[] = 'ORDER BY ' . implode(', ', $query->getOrderBy());
        }

        if (!empty($query->getLimit())) {
            $sql[] = 'LIMIT ' . $query->getLimit();
        }

        return implode("\n", $sql);
    }
}