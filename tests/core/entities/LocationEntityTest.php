<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;

class LocationEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new LocationEntity()
        );
    }

    public function testIdProperty(): void
    {
        $location = new LocationEntity();
        $location->setId(TestConstants::TEST_LOCATION_ID);
        $this->assertEquals(
            TestConstants::TEST_LOCATION_ID,
            $location->getId()
        );
    }

    public function testLocationProperty(): void
    {
        $location = new LocationEntity();
        $location->setLocation(TestConstants::TEST_LOCATION_NAME);
        $this->assertEquals(
            TestConstants::TEST_LOCATION_NAME,
            $location->getLocation()
        );
    }

    public function testDeaneryProperty(): void
    {
        $location = new LocationEntity();
        $location->setDeanery(new DeaneryEntity());
        $this->assertInstanceOf(
            DeaneryEntity::class,
            $location->getDeanery()
        );
    }

    public function testDedicationProperty(): void
    {
        $location = new LocationEntity();
        $location->setDedication(TestConstants::TEST_LOCATION_DEDICATION);
        $this->assertEquals(
            TestConstants::TEST_LOCATION_DEDICATION,
            $location->getDedication()
        );
    }

    public function testTenorWeightProperty(): void
    {
        $location = new LocationEntity();
        $location->setTenorWeight(TestConstants::TEST_LOCATION_WEIGHT);
        $this->assertEquals(
            TestConstants::TEST_LOCATION_WEIGHT,
            $location->getTenorWeight()
        );
    }

    public function testNumberOfBellsProperty(): void
    {
        $location = new LocationEntity();
        $location->setNumberOfBells(
            TestConstants::TEST_LOCATION_NUMBER_OF_BELLS
        );
        $this->assertEquals(
            TestConstants::TEST_LOCATION_NUMBER_OF_BELLS,
            $location->getNumberOfBells(),
            'Incorrect number of bells when integer'
        );
        $location->setNumberOfBells(null);
        $this->assertNull(
            $location->getNumberOfBells(),
            'Incorrect number of bells when null'
        );
    }
}
