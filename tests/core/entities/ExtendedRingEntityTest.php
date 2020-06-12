<?php

declare(strict_types=1);

namespace core\entities;

use DateTime;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\ExtendedRingEntity;
use DrlArchive\core\entities\ExtendedRingPartEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\entities\LocationEntity;
use PHPUnit\Framework\TestCase;

class ExtendedRingEntityTest extends TestCase
{

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new ExtendedRingEntity()
        );
    }

    public function testIdProperty(): void
    {
        $extendedRing = new ExtendedRingEntity();
        $extendedRing->setId(3);

        $this->assertEquals(
            3,
            $extendedRing->getId()
        );
    }

    public function testLocationProperty(): void
    {
        $extendedRing = new ExtendedRingEntity();
        $extendedRing->setLocation(new LocationEntity());

        $this->assertInstanceOf(
            LocationEntity::class,
            $extendedRing->getLocation()
        );
    }

    public function testDateProperty(): void
    {
        $extendedRing = new ExtendedRingEntity();
        $extendedRing->setDate(new DateTime());

        $this->assertInstanceOf(
            DateTime::class,
            $extendedRing->getDate()
        );
    }

    public function testFootnoteProperty(): void
    {
        $extendedRing = new ExtendedRingEntity();
        $extendedRing->setFootnote('Test');

        $this->assertEquals(
            'Test',
            $extendedRing->getFootnote()
        );
    }

    public function testName(): void
    {
        $extendedRing = new ExtendedRingEntity();
        $extendedRing->setName('Test');

        $this->assertEquals(
            'Test',
            $extendedRing->getName()
        );
    }

    public function testParts(): void
    {
        $extendedRing = new ExtendedRingEntity();
        $extendedRing->setParts(
            [
                new ExtendedRingPartEntity(),
                new ExtendedRingPartEntity(),
            ]
        );

        $this->assertInstanceOf(
            ExtendedRingPartEntity::class,
            $extendedRing->getParts()[0]
        );
    }

    public function testJudges(): void
    {
        $extendedRing = new ExtendedRingEntity();
        $extendedRing->setJudges(
            [
                new JudgeEntity(),
                new JudgeEntity()
            ]
        );

        $this->assertInstanceOf(
            JudgeEntity::class,
            $extendedRing->getJudges()[0]
        );
    }
}
