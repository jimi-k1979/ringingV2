<?php
declare(strict_types=1);

namespace DrlArchive\implementation\factories\managers;


use DrlArchive\core\interfaces\factories\managers\TransactionManagerFactoryInterface;
use DrlArchive\core\interfaces\repositories\TransactionManagerInterface;
use DrlArchive\implementation\repositories\sql\Database;
use DrlArchive\implementation\repositories\sql\MysqlTransactionManager;

class TransactionManagerFactory implements TransactionManagerFactoryInterface
{
    public function create(): TransactionManagerInterface
    {
        return new MysqlTransactionManager(Database::createConnection());
    }

}