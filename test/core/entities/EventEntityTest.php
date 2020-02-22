<?php
declare(strict_types=1);

namespace core\entities;

use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\EventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\entities\LocationEntity;
use PHPUnit\Framework\TestCase;

class EventEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new EventEntity()
        );
    }

    public function testIdProperty(): void
    {
        $event = new EventEntity();
        $event->setId(4);
        $this->assertEquals(
            4,
            $event->getId()
        );
    }

    public function testYearProperty(): void
    {
        $event = new EventEntity();
        $event->setYear('2345');
        $this->assertEquals(
            '2345',
            $event->getYear()
        );
    }

    public function testCompetitionEntity(): void
    {
        $event = new EventEntity();
        $event->setCompetition(new DrlCompetitionEntity());
        $this->assertInstanceOf(
            AbstractCompetitionEntity::class,
            $event->getCompetition()
        );
    }

    public function testLocationProperty(): void
    {
        $event = new EventEntity();
        $event->setLocation(new LocationEntity());
        $this->assertInstanceOf(
            LocationEntity::class,
            $event->getLocation()
        );
    }

    public function testJudgesProperty(): void
    {
        $event = new EventEntity();
        $event->setJudges([
            new JudgeEntity(),
            new JudgeEntity(),
        ]);
        $this->assertIsArray(
            $event->getJudges()
        );
        $this->assertInstanceOf(
            JudgeEntity::class,
            $event->getJudges()[0]
        );
    }
}
