<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories\doctrine;


use DrlArchive\core\interfaces\factories\repositories\CompetitionRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\implementation\repositories\doctrine\CompetitionDoctrine;
use DrlArchive\implementation\repositories\doctrine\DoctrineDatabase;

class CompetitionDoctrineFactory implements
    CompetitionRepositoryFactoryInterface
{

    public function create(): CompetitionRepositoryInterface
    {
        return new CompetitionDoctrine(DoctrineDatabase::createConnection());
    }
}
