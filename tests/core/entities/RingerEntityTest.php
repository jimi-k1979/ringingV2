<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\TestConstants;
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
        $ringer->setId(TestConstants::TEST_RINGER_ID);
        $this->assertEquals(
            TestConstants::TEST_RINGER_ID,
            $ringer->getId()
        );
    }

    public function testFirstNameProperty(): void
    {
        $ringer = new RingerEntity();
        $ringer->setFirstName(TestConstants::TEST_RINGER_FIRST_NAME);
        $this->assertEquals(
            TestConstants::TEST_RINGER_FIRST_NAME,
            $ringer->getFirstName()
        );
    }

    public function testLastNameProperty(): void
    {
        $ringer = new RingerEntity();
        $ringer->setLastName(TestConstants::TEST_RINGER_LAST_NAME);
        $this->assertEquals(
            TestConstants::TEST_RINGER_LAST_NAME,
            $ringer->getLastName()
        );
    }

    public function testNotes(): void
    {
        $ringer = new RingerEntity();
        $ringer->setNotes(TestConstants::TEST_RINGER_NOTES);
        $this->assertEquals(
            TestConstants::TEST_RINGER_NOTES,
            $ringer->getNotes()
        );
    }

    public function testGetFullName(): void
    {
        $ringer = new RingerEntity();
        $ringer->setFirstName(TestConstants::TEST_RINGER_FIRST_NAME);
        $ringer->setLastName(TestConstants::TEST_RINGER_LAST_NAME);
        $this->assertEquals(
            TestConstants::TEST_RINGER_FIRST_NAME . ' ' . TestConstants::TEST_RINGER_LAST_NAME,
            $ringer->getFullName()
        );
    }
}
