<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use DrlArchive\traits\CreateMockDeaneryTrait;

class DeaneryDummy implements DeaneryRepositoryInterface
{
    use CreateMockDeaneryTrait;

    public function getDeaneryByName(string $name): DeaneryEntity
    {
        return $this->createMockDeanery();
    }

    public function selectDeanery(int $id): DeaneryEntity
    {
        return $this->createMockDeanery();
    }
}
