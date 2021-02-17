<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories\doctrine;


use DrlArchive\core\interfaces\factories\repositories\DeaneryRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use DrlArchive\implementation\repositories\doctrine\DeaneryDoctrine;
use DrlArchive\implementation\repositories\doctrine\DoctrineDatabase;

class DeaneryDoctrineFactory implements
    DeaneryRepositoryFactoryInterface
{

    public function create(): DeaneryRepositoryInterface
    {
        return new DeaneryDoctrine(DoctrineDatabase::createConnection());
    }
}
