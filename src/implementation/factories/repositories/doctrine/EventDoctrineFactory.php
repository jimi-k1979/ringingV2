<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories\doctrine;


use Doctrine\DBAL\Exception;
use DrlArchive\core\interfaces\factories\repositories\EventRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\implementation\repositories\doctrine\DoctrineDatabase;
use DrlArchive\implementation\repositories\doctrine\EventDoctrine;

class EventDoctrineFactory implements EventRepositoryFactoryInterface
{
    /**
     * @return EventRepositoryInterface
     * @throws Exception
     */
    public function create(): EventRepositoryInterface
    {
        return new EventDoctrine(DoctrineDatabase::createConnection());
    }
}
