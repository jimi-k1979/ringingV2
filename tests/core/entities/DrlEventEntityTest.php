<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

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
        $this->assertNull(
            $event->getId(),
            'Incorrect initial value'
        );
        $event->setId(TestConstants::TEST_EVENT_ID);
        $this->assertEquals(
            TestConstants::TEST_EVENT_ID,
            $event->getId(),
            'Getter and setter failure'
        );
    }

    public function testYearProperty(): void
    {
        $event = new DrlEventEntity();
        $this->assertEquals(
            '',
            $event->getYear(),
            'incorrect initial value'
        );
        $event->setYear(TestConstants::TEST_EVENT_YEAR);
        $this->assertEquals(
            TestConstants::TEST_EVENT_YEAR,
            $event->getYear(),
            'Getter and setter failure'
        );
    }

    public function testCompetitionEntity(): void
    {
        $event = new DrlEventEntity();
        $this->assertNull(
            $event->getCompetition(),
            'Incorrect initial value'
        );
        $event->setCompetition(new DrlCompetitionEntity());
        $this->assertInstanceOf(
            AbstractCompetitionEntity::class,
            $event->getCompetition(),
            'getter and setter failure'
        );
    }

    public function testLocationProperty(): void
    {
        $event = new DrlEventEntity();
        $this->assertNull(
            $event->getLocation(),
            'Incorrect initial value'
        );
        $event->setLocation(new LocationEntity());
        $this->assertInstanceOf(
            LocationEntity::class,
            $event->getLocation(),
            'getter and setter failure'
        );
    }

    public function testJudgesProperty(): void
    {
        $event = new DrlEventEntity();
        $this->assertEquals(
            [],
            $event->getJudges(),
            'Incorrect initial value'
        );
        $event->setJudges(
            [
                new JudgeEntity(),
                new JudgeEntity(),
            ]
        );
        $this->assertIsArray(
            $event->getJudges(),
            'getter and setter failure'
        );
        $this->assertInstanceOf(
            JudgeEntity::class,
            $event->getJudges()[0],
            'first element of incorrect type'
        );
    }

    public function testUnusualTowerProperty(): void
    {
        $event = new DrlEventEntity();
        $this->assertFalse(
            $event->isUnusualTower(),
            'Incorrect initial value'
        );
        $event->setUnusualTower(true);
        $this->assertTrue(
            $event->isUnusualTower(),
            'getter and setter failure'
        );
    }

    public function testTotalFaultsProperty(): void
    {
        $event = new DrlEventEntity();
        $this->assertNull(
            $event->getTotalFaults(),
            'Incorrect initial value'
        );
        $event->setTotalFaults(TestConstants::TEST_EVENT_TOTAL_FAULTS);
        $this->assertEquals(
            TestConstants::TEST_EVENT_TOTAL_FAULTS,
            $event->getTotalFaults(),
            'getter and setter failure'
        );
    }

    public function testMeanFaultsProperty(): void
    {
        $event = new DrlEventEntity();
        $this->assertNull(
            $event->getMeanFaults(),
            'Incorrect initial value'
        );
        $event->setMeanFaults(TestConstants::TEST_EVENT_MEAN_FAULTS);
        $this->assertEquals(
            TestConstants::TEST_EVENT_MEAN_FAULTS,
            $event->getMeanFaults(),
            'getter and setter failure'
        );
    }

    public function testWinningMarginProperty(): void
    {
        $event = new DrlEventEntity();
        $this->assertNull(
            $event->getWinningMargin(),
            'Incorrect initial value'
        );
        $event->setWinningMargin(TestConstants::TEST_EVENT_WINNING_MARGIN);
        $this->assertEquals(
            TestConstants::TEST_EVENT_WINNING_MARGIN,
            $event->getWinningMargin(),
            'getter and setter failure'
        );
    }


}
