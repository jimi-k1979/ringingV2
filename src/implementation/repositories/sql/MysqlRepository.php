<?php
declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\implementation\entities\DatabaseQueryBuilder;
use DrlArchive\implementation\interfaces\SqlDatabaseInterface;

class MysqlRepository extends Repository
{
    private const EMPTY_PASSWORD = '';
    private const EMPTY_USER = '';

    public const ORDER_BY_DESC = ' DESC';

    public const EXCEPTION_NO_FIELDS_IN_SELECT_QUERY = 1210;
    public const EXCEPTION_NO_TABLES_IN_SELECT_QUERY = 1210;

    /**
     * @var Database
     */
    protected $database;

    /**
     * MysqlRepository constructor.
     * @param SqlDatabaseInterface $database
     */
    public function __construct(SqlDatabaseInterface $database)
    {
        $this->database = $database;
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