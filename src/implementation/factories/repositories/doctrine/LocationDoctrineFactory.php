<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories\doctrine;


use DrlArchive\core\interfaces\factories\repositories\LocationRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use DrlArchive\implementation\repositories\doctrine\DoctrineDatabase;
use DrlArchive\implementation\repositories\doctrine\LocationDoctrine;

class LocationDoctrineFactory implements
    LocationRepositoryFactoryInterface
{

    public function create(): LocationRepositoryInterface
    {
        return new LocationDoctrine(DoctrineDatabase::createConnection());
    }
}
