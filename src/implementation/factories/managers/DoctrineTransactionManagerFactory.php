<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\managers;


use DrlArchive\core\interfaces\factories\managers\TransactionManagerFactoryInterface;
use DrlArchive\core\interfaces\managers\TransactionManagerInterface;
use DrlArchive\implementation\repositories\doctrine\DoctrineDatabase;
use DrlArchive\implementation\repositories\doctrine\DoctrineTransactionManager;

class DoctrineTransactionManagerFactory implements
    TransactionManagerFactoryInterface
{

    public function create(): TransactionManagerInterface
    {
        return new DoctrineTransactionManager(
            DoctrineDatabase::createConnection()
        );
    }
}
