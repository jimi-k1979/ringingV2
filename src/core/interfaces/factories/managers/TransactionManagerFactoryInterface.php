<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\factories\managers;


use DrlArchive\core\interfaces\managers\TransactionManagerInterface;

interface TransactionManagerFactoryInterface
{
    public function create(): TransactionManagerInterface;
}
