<?php
declare(strict_types=1);

namespace traits;


use DrlArchive\core\entities\LocationEntity;

trait CreateMockLocationTrait
{

    use CreateMockDeaneryTrait;

    private function CreateMockLocation(): LocationEntity
    {
        $entity = new LocationEntity();
        $entity->setId(999);
        $entity->setLocation('Test tower');
        $entity->setDeanery($this->createMockDeanery());
        $entity->setDedication('S Test');
        $entity->setTenorWeight('test cwt');

        return $entity;
    }
}