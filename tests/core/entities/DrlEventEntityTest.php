<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\AbstractEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;

class DrlEventEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $entity = new DrlEventEntity();
        $this->assertInstanceOf(
            Entity::class,
            $entity
        );

        $this->assertInstanceOf(
            AbstractEventEntity::class,
            $entity
        );
    }

    public function testIdProperty(): void
    {
        $event = new DrlEventEntity();
        $event->setId(TestConstants::TEST_EVENT_ID);
        $this->assertEquals(
            TestConstants::TEST_EVENT_ID,
            $event->getId()
        );
    }

    public function testYearProperty(): void
    {
        $event = new DrlEventEntity();
        $event->setYear(TestConstants::TEST_EVENT_YEAR);
        $this->assertEquals(
            TestConstants::TEST_EVENT_YEAR,
            $event->getYear()
        );
    }

    public function testCompetitionEntity(): void
    {
        $event = new DrlEventEntity();
        $event->setCompetition(new DrlCompetitionEntity());
        $this->assertInstanceOf(
            AbstractCompetitionEntity::class,
            $event->getCompetition()
        );
    }

    public function testLocationProperty(): void
    {
        $event = new DrlEventEntity();
        $event->setLocation(new LocationEntity());
        $this->assertInstanceOf(
            LocationEntity::class,
            $event->getLocation()
        );
    }

    public function testJudgesProperty(): void
    {
        $event = new DrlEventEntity();
        $event->setJudges(
            [
                new JudgeEntity(),
                new JudgeEntity(),
            ]
        );
        $this->assertIsArray(
            $event->getJudges()
        );
        $this->assertInstanceOf(
            JudgeEntity::class,
            $event->getJudges()[0]
        );
    }

    public function testUnusualTowerProperty(): void
    {
        $event = new DrlEventEntity();
        $event->setUnusualTower(true);
        $this->assertTrue(
            $event->isUnusualTower()
        );
    }
}
