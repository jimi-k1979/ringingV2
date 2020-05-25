<?php

declare(strict_types=1);

namespace DrlArchive\implementation\entities;


class DatabaseQueryBuilder
{
    public const ORDER_BY_DESC = ' DESC';

    /**
     * @var array
     */
    private $fields = [];
    /**
     * @var array
     */
    private $tablesAndJoins = [];
    /**
     * @var array
     */
    private $whereClauses = [];
    /**
     * @var array
     */
    private $groupBy = [];
    /**
     * @var array
     */
    private $orderBy = [];
    /**
     * @var string
     */
    private $limit = '';
    /**
     * @var bool
     */
    private $distinctQuery = false;

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    public function getTablesAndJoins(): array
    {
        return $this->tablesAndJoins;
    }

    /**
     * @param array $tablesAndJoins
     */
    public function setTablesAndJoins(array $tablesAndJoins): void
    {
        $this->tablesAndJoins = $tablesAndJoins;
    }

    /**
     * @return array
     */
    public function getWhereClauses(): array
    {
        return $this->whereClauses;
    }

    /**
     * @param array $whereClauses
     */
    public function setWhereClauses(array $whereClauses): void
    {
        $this->whereClauses = $whereClauses;
    }

    /**
     * @return array
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * @param array $groupBy
     */
    public function setGroupBy(array $groupBy): void
    {
        $this->groupBy = $groupBy;
    }

    /**
     * @return array
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @param array $orderBy
     */
    public function setOrderBy(array $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    /**
     * @return string
     */
    public function getLimit(): string
    {
        return $this->limit;
    }

    /**
     * @param string $limit
     */
    public function setLimit(string $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return bool
     */
    public function isDistinctQuery(): bool
    {
        return $this->distinctQuery;
    }

    /**
     * @param bool $distinctQuery
     */
    public function setDistinctQuery(bool $distinctQuery): void
    {
        $this->distinctQuery = $distinctQuery;
    }

}