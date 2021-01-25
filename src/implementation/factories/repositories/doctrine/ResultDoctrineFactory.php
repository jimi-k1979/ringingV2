<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories\doctrine;


use DrlArchive\core\interfaces\factories\repositories\ResultRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use DrlArchive\implementation\repositories\doctrine\DoctrineDatabase;
use DrlArchive\implementation\repositories\doctrine\ResultDoctrine;

class ResultDoctrineFactory implements
    ResultRepositoryFactoryInterface
{

    public function create(): ResultRepositoryInterface
    {
        return new ResultDoctrine(DoctrineDatabase::createConnection());
    }
}
