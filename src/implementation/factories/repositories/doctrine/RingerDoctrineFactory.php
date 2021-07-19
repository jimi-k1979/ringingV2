<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories\doctrine;


use DrlArchive\core\interfaces\factories\repositories\RingerRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use DrlArchive\core\interfaces\repositories\RingerRepositoryInterface;
use DrlArchive\implementation\repositories\doctrine\DoctrineDatabase;
use DrlArchive\implementation\repositories\doctrine\ResultDoctrine;
use DrlArchive\implementation\repositories\doctrine\RingerDoctrine;

class RingerDoctrineFactory implements
    RingerRepositoryFactoryInterface
{

    public function create(): RingerRepositoryInterface
    {
        return new RingerDoctrine(DoctrineDatabase::createConnection());
    }
}
