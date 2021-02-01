<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\RingerEntity;
use PHPUnit\Framework\TestCase;

class RingerEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new RingerEntity()
        );
    }

    public function testIdProperty(): void
    {
        $ringer = new RingerEntity();
        $ringer->setId(123);
        $this->assertEquals(
            123,
            $ringer->getId()
        );
    }

    public function testFirstNameProperty(): void
    {
        $ringer = new RingerEntity();
        $ringer->setFirstName('Test');
        $this->assertEquals(
            'Test',
            $ringer->getFirstName()
        );
    }

    public function testLastNameProperty(): void
    {
        $ringer = new RingerEntity();
        $ringer->setLastName('Test');
        $this->assertEquals(
            'Test',
            $ringer->getLastName()
        );
    }

    public function testNotes(): void
    {
        $ringer = new RingerEntity();
        $ringer->setNotes('Test');
        $this->assertEquals(
            'Test',
            $ringer->getNotes()
        );
    }

    public function testGetFullName(): void
    {
        $ringer = new RingerEntity();
        $ringer->setFirstName('Test');
        $ringer->setLastName('Ringer');
        $this->assertEquals(
            'Test Ringer',
            $ringer->getFullName()
        );
    }
}
