<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories\doctrine;


use DrlArchive\core\interfaces\factories\repositories\TeamRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use DrlArchive\implementation\repositories\doctrine\DoctrineDatabase;
use DrlArchive\implementation\repositories\doctrine\TeamDoctrine;

class TeamDoctrineFactory implements TeamRepositoryFactoryInterface
{

    public function create(): TeamRepositoryInterface
    {
        return new TeamDoctrine(DoctrineDatabase::createConnection());
    }
}
