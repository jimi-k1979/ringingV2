<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\factories\repositories;


use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;

interface LocationRepositoryFactoryInterface
{
    public function create(): LocationRepositoryInterface;
}