<?php
declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;

class DeanerySql extends MysqlRepository implements DeaneryRepositoryInterface
{

    public function getDeaneryByName(string $name): DeaneryEntity
    {
        return new DeaneryEntity();
    }

    public function selectDeanery(int $id): DeaneryEntity
    {
        return new DeaneryEntity();
    }
}