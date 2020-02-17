<?php
declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;

class LocationSql
    extends MysqlRepository
    implements LocationRepositoryInterface
{

    public function insertLocation(LocationEntity $locationEntity): LocationEntity
    {
        // TODO: Implement createLocation() method.
    }

    public function selectLocation(int $locationId): LocationEntity
    {
        // TODO: Implement readLocation() method.
    }
}