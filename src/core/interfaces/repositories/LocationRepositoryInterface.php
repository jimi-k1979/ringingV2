<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\LocationEntity;

interface LocationRepositoryInterface
{
    const UNABLE_TO_INSERT_EXCEPTION = 2101;
    const NO_ROWS_FOUND_EXCEPTION = 2102;

    public function insertLocation(
        LocationEntity $locationEntity
    ): LocationEntity;

    public function selectLocation(int $locationId): LocationEntity;

}