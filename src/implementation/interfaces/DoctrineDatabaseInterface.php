<?php

declare(strict_types=1);

namespace DrlArchive\implementation\interfaces;


use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Driver\Statement;

interface DoctrineDatabaseInterface extends SqlDatabaseInterface
{

    public function fetchReadOnlyStatement(
        string $query,
        array $params = []
    ): Statement;

    public function createQueryBuilder(): QueryBuilder;
}
