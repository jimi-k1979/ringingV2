<?php
declare(strict_types=1);

namespace core\entities;

use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\ExtendedRingPartEntity;
use DrlArchive\core\entities\ExtendedRingPartRingerEntity;
use DrlArchive\core\entities\RingerEntity;
use PHPUnit\Framework\TestCase;

class ExtendedRingPartRingerEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new ExtendedRingPartRingerEntity()
        );
    }

    public function testIdProperty(): void
    {
        $entity = new ExtendedRingPartRingerEntity();
        $entity->setId(123);

        $this->assertEquals(
            123,
            $entity->getId(123)
        );
    }

    public function testExtendedRingPartProperty(): void
    {
        $entity = new ExtendedRingPartRingerEntity();
        $entity->setExtendedRingPart(new ExtendedRingPartEntity());

        $this->assertInstanceOf(
            ExtendedRingPartEntity::class,
            $entity->getExtendedRingPart()
        );
    }

    public function testRingerProperty(): void
    {
        $entity = new ExtendedRingPartRingerEntity();
        $entity->setRinger(new RingerEntity());

        $this->assertInstanceOf(
            RingerEntity::class,
            $entity->getRinger()
        );
    }

    public function testBellProperty(): void
    {
        $entity = new ExtendedRingPartRingerEntity();
        $entity->setBell('Strapper');

        $this->assertEquals(
            'Strapper',
            $entity->getBell()
        );
    }

    public function testConductorProperty(): void
    {
        $entity = new ExtendedRingPartRingerEntity();
        $entity->setConductor(true);

        $this->assertTrue(
            $entity->isConductor()
        );
    }
}
