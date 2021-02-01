<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/** @noinspection PhpUndefinedVariableInspection */

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\implementation\interfaces\DoctrineDatabaseInterface;
use DrlArchive\Settings;
use Throwable;

class DoctrineDatabase implements DoctrineDatabaseInterface
{

    private const PDO_MYSQL_DRIVER = 'pdo_mysql';

    public const ERROR_CONSTRAINT = 23000;
    public const ERROR_CONNECTION = 2002;
    public const ERROR_ACCESS_DENIED = 1045;
    public const ERROR_DATABASE_NAME = 1049;
    public const ERROR_NO_TABLE_FOUND = '42S02';
    public const ERROR_NO_CODE = 0;

    private static ?DoctrineDatabase $database = null;
    private Connection $connection;
    private int $transactionCount = 0;

    /**
     * DoctrineDatabase constructor.
     * @param Connection $connection
     */
    private function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return static
     * @throws Exception
     */
    public static function createConnection(): self
    {
        if (self::$database === null) {
            self::$database = new DoctrineDatabase(self::createPDO());
        }

        return self::$database;
    }

    /**
     * @return Connection
     * @throws Exception
     */
    private static function createPDO(): Connection
    {
        $connectionParams = [
            'dbname' => Settings::DB_SCHEMA,
            'user' => Settings::DB_USER,
            'password' => Settings::DB_PASSWORD,
            'host' => Settings::DB_HOST,
            'driver' => self::PDO_MYSQL_DRIVER,
        ];

        return DriverManager::getConnection($connectionParams);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return int
     * @throws CleanArchitectureException
     */
    public function execute(string $sql, array $params = []): int
    {
        try {
            return $this->connection->executeStatement($sql, $params);
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    /**
     * @param Throwable $e
     * @throws CleanArchitectureException
     */
    private function handleError(Throwable $e): void
    {
        switch ($e->getCode()) {
            case self::ERROR_CONNECTION:
                throw new RepositoryConnectionErrorException(
                    'Database connection error',
                    Repository::REPOSITORY_ERROR_CONNECTION
                );

            default:
                throw new GeneralRepositoryErrorException(
                    'Unknown database error',
                    Repository::REPOSITORY_ERROR_UNKNOWN
                );
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array|array[]
     * @throws CleanArchitectureException
     */
    public function executeAndFetch(string $sql, array $params = []): array
    {
        try {
            return $this->query($sql, $params);
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @param int $queryType
     * @return array|array[]|false|mixed
     * @throws CleanArchitectureException
     */
    public function query(
        string $sql,
        array $params = [],
        int $queryType = self::FETCH_MULTI_ROW
    ) {
        try {
            $statement = $this->connection->executeQuery($sql, $params);

            if ($queryType === self::FETCH_MULTI_ROW) {
                return $statement->fetchAllAssociative();
            } elseif ($queryType === self::FETCH_SINGLE_ROW) {
                return $statement->fetchAssociative();
            } else {
                return $statement->fetchOne();
            }
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    /**
     * @throws CleanArchitectureException
     */
    public function startTransaction(): void
    {
        try {
            if ($this->transactionCount === 0) {
                $this->connection->beginTransaction();
            }
            $this->transactionCount++;
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    /**
     * @throws CleanArchitectureException
     */
    public function commitTransaction(): void
    {
        try {
            if (--$this->transactionCount === 0) {
                $this->connection->commit();
            }
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    /**
     * @throws CleanArchitectureException
     */
    public function rollbackTransaction(): void
    {
        try {
            if (--$this->transactionCount === 0) {
                $this->connection->rollBack();
            }
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    /**
     * @return string
     * @throws CleanArchitectureException
     */
    public function getLastInsertId(): string
    {
        try {
            return $this->connection->lastInsertId();
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * @param string $query
     * @param array $params
     * @return Statement
     * @throws CleanArchitectureException
     */
    public function fetchReadOnlyStatement(
        string $query,
        array $params = []
    ): Statement {
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }
}
