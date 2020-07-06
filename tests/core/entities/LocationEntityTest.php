<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\LocationEntity;
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
        $location->setId(4);
        $this->assertEquals(
            4,
            $location->getId()
        );
    }

    public function testLocationProperty(): void
    {
        $location = new LocationEntity();
        $location->setLocation('test');
        $this->assertEquals(
            'test',
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
        $location->setDedication('Test');
        $this->assertEquals(
            'Test',
            $location->getDedication()
        );
    }

    public function testTenorWeightProperty(): void
    {
        $location = new LocationEntity();
        $location->setTenorWeight('Test');
        $this->assertEquals(
            'Test',
            $location->getTenorWeight()
        );
    }

    public function testNumberOfBellsProperty(): void
    {
        $location = new LocationEntity();
        $location->setNumberOfBells(6);
        $this->assertEquals(
            6,
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
