<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

use DateTime;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\ExtendedRingPartEntity;
use PHPUnit\Framework\TestCase;

class ExtendedRingPartEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new ExtendedRingPartEntity()
        );
    }

    public function testIdProperty(): void
    {
        $entity = new ExtendedRingPartEntity();
        $entity->setId(123);

        $this->assertEquals(
            123,
            $entity->getId()
        );
    }

    public function testNumberOfChanges(): void
    {
        $entity = new ExtendedRingPartEntity();
        $entity->setNumberOfChanges(123);

        $this->assertEquals(
            123,
            $entity->getNumberOfChanges()
        );
    }

    public function testTime(): void
    {
        $entity = new ExtendedRingPartEntity();
        $entity->setTime(new DateTime());

        $this->assertInstanceOf(
            DateTime::class,
            $entity->getTime()
        );
    }

    public function testName(): void
    {
        $entity = new ExtendedRingPartEntity();
        $entity->setName('Test');

        $this->assertEquals(
            'Test',
            $entity->getName()
        );
    }
}
