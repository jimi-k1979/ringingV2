<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories;


use DrlArchive\core\interfaces\factories\repositories\SecurityRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;
use DrlArchive\implementation\repositories\existing\ExistingSecurityRepository;

class SecurityRepositoryFactory implements SecurityRepositoryFactoryInterface
{

    public function create(): SecurityRepositoryInterface
    {
        return new ExistingSecurityRepository();
    }
}