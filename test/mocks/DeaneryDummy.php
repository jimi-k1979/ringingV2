<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;

class DeaneryDummy implements DeaneryRepositoryInterface
{

    public function getDeaneryByName(string $name): DeaneryEntity
    {
        return new DeaneryEntity();
    }
}