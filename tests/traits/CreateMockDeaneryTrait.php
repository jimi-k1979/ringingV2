<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\TestConstants;

trait CreateMockDeaneryTrait
{
    private function createMockDeanery(): DeaneryEntity
    {
        $entity = new DeaneryEntity();
        $entity->setId(TestConstants::TEST_DEANERY_ID);
        $entity->setName(TestConstants::TEST_DEANERY_NAME);
        $entity->setRegion(TestConstants::TEST_DEANERY_REGION);

        return $entity;
    }
}