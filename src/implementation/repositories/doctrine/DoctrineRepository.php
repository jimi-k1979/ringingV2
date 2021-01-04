<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\implementation\interfaces\DoctrineDatabaseInterface;

class DoctrineRepository extends Repository
{
    /**
     * @var DoctrineDatabase
     */
    protected $database;

    /**
     * MysqlRepository constructor.
     * @param DoctrineDatabaseInterface $database
     */
    public function __construct(DoctrineDatabaseInterface $database)
    {
        $this->database = $database;
    }

}
