<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories\doctrine;

use DrlArchive\core\interfaces\factories\repositories\CompositionRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\CompositionRepositoryInterface;
use DrlArchive\implementation\repositories\doctrine\CompositionDoctrine;
use DrlArchive\implementation\repositories\doctrine\DoctrineDatabase;

class CompositionDoctrineFactory implements
    CompositionRepositoryFactoryInterface
{

    public function create(): CompositionRepositoryInterface
    {
        return new CompositionDoctrine(DoctrineDatabase::createConnection());
    }
}
