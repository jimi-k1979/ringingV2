<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\interfaces\managers\TransactionManagerInterface;

class TransactionManagerSpy implements TransactionManagerInterface
{
    protected bool $commitTransactionCalled = false;
    protected bool $rollbackTransactionCalled = false;
    private bool $startTransactionCalled = false;

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