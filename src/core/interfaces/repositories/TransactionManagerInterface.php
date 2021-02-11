<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


interface TransactionManagerInterface
{
    public function startTransaction(): void;

    public function commitTransaction(): void;

    public function rollbackTransaction(): void;
}
