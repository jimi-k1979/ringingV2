<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\interfaces\repositories\TransactionManagerInterface;

class TransactionManagerDummy implements TransactionManagerInterface
{

    public function startTransaction(): void
    {
    }

    public function commitTransaction(): void
    {
    }

    public function rollbackTransaction(): void
    {
    }
}