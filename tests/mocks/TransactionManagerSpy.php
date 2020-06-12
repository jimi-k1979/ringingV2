<?php

declare(strict_types=1);

namespace mocks;


use DrlArchive\core\interfaces\repositories\TransactionManagerInterface;

class TransactionManagerSpy implements TransactionManagerInterface
{
    protected $commitTransactionCalled = false;
    protected $rollbackTransactionCalled = false;
    private $startTransactionCalled = false;

    public function startTransaction(): void
    {
        $this->startTransactionCalled = true;
    }


    public function commitTransaction(): void
    {
        $this->commitTransactionCalled = true;
    }


    public function rollbackTransaction(): void
    {
        $this->rollbackTransactionCalled = true;
    }


    public function hasStartTransactionBeenCalled(): bool
    {
        return $this->startTransactionCalled;
    }

    public function hasCommitTransactionBeenCalled(): bool
    {
        return $this->commitTransactionCalled;
    }


    public function hasRollbackTransactionBeenCalled(): bool
    {
        return $this->rollbackTransactionCalled;
    }
}