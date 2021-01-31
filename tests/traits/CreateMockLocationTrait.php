<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\LocationEntity;
use DrlArchive\TestConstants;

trait CreateMockLocationTrait
{

    use CreateMockDeaneryTrait;

    private function createMockLocation(): LocationEntity
    {
        $entity = new LocationEntity();
        $entity->setId(TestConstants::TEST_LOCATION_ID);
        $entity->setLocation(TestConstants::TEST_LOCATION_NAME);
        $entity->setDeanery($this->createMockDeanery());
        $entity->setDedication(TestConstants::TEST_LOCATION_DEDICATION);
        $entity->setTenorWeight(TestConstants::TEST_LOCATION_WEIGHT);
        $entity->setNumberOfBells(TestConstants::TEST_LOCATION_NUMBER_OF_BELLS);

        return $entity;
    }
}
