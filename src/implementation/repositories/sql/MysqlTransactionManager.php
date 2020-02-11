<?php
declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\interfaces\repositories\TransactionManagerInterface;

class MysqlTransactionManager implements TransactionManagerInterface
{
    /**
     * @var Database|null
     */
    private $database;


    public function __construct(Database $database)
    {
        $this->database = $database;
    }


    public function startTransaction(): void
    {
        $this->database->startTransaction();
    }


    public function commitTransaction(): void
    {
        $this->database->commitTransaction();
    }


    public function rollbackTransaction(): void
    {
        $this->database->rollbackTransaction();
    }
}