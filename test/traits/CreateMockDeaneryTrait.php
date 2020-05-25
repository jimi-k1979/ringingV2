<?php
declare(strict_types=1);

namespace test\traits;


use DrlArchive\core\entities\DeaneryEntity;

trait CreateMockDeaneryTrait
{
    private function createMockDeanery(): DeaneryEntity
    {
        $entity = new DeaneryEntity();
        $entity->setId(123);
        $entity->setName('Test deanery');
        $entity->setLocationInCounty('south');

        return $entity;
    }
}