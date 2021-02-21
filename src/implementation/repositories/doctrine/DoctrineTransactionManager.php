<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interfaces\managers\TransactionManagerInterface;

class DoctrineTransactionManager implements TransactionManagerInterface
{

    /**
     * @var DoctrineDatabase
     */
    private DoctrineDatabase $database;

    public function __construct(DoctrineDatabase $database)
    {
        $this->database = $database;
    }

    /**
     * @throws CleanArchitectureException
     */
    public function startTransaction(): void
    {
        $this->database->startTransaction();
    }

    /**
     * @throws CleanArchitectureException
     */
    public function commitTransaction(): void
    {
        $this->database->commitTransaction();
    }

    /**
     * @throws CleanArchitectureException
     */
    public function rollbackTransaction(): void
    {
        $this->database->rollbackTransaction();
    }
}
