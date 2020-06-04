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
    public const EXCEPTION_FIELD_COUNT_NOT_THE_SAME_IN_UNION = 1211; // TODO - document!
    public const EXCEPTION_ORDER_BY_IN_SUB_UNION_QUERY = 1212; // TODO - document!

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

    /**
     * @param DatabaseQueryBuilder[] $queryParts
     * @param array $orderBy
     * @param bool $isUnionAll
     * @return string
     * @throws GeneralRepositoryErrorException
     */
    public function buildUnionSelectQuery(
        array $queryParts,
        array $orderBy = [],
        bool $isUnionAll = false
    ): string {
        $fieldCount = count($queryParts[0]->getFields());
        $subQueries = [];

        foreach ($queryParts as $queryPart) {
            if (count($queryPart->getFields()) !== $fieldCount) {
                throw new GeneralRepositoryErrorException(
                    'Field count is not identical in each sub query',
                    self::EXCEPTION_FIELD_COUNT_NOT_THE_SAME_IN_UNION
                );
            }
            if (!empty($queryPart->getOrderBy())) {
                throw new GeneralRepositoryErrorException(
                    'Order by clauses cannot be in the sub query',
                    self::EXCEPTION_ORDER_BY_IN_SUB_UNION_QUERY
                );
            }
            $subQueries[] = $this->buildSelectQuery($queryPart);
        }

        if ($isUnionAll) {
            $unionQuery = implode(
                ' UNION ALL ',
                $subQueries
            );
        } else {
            $unionQuery = implode(
                ' UNION ',
                $subQueries
            );
        }

        if (!empty($orderBy)) {
            $orderByClause = 'ORDER BY ' . implode(', ', $orderBy);
        } else {
            $orderByClause = '';
        }

        return $unionQuery . $orderByClause;
    }
}