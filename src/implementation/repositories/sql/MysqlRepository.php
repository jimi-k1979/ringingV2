<?php
declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\implementation\interfaces\SqlDatabaseInterface;

class MysqlRepository extends Repository
{
    private const EMPTY_PASSWORD = '';
    private const EMPTY_USER = '';

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

}