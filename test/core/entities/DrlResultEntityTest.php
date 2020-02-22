<?php
declare(strict_types=1);

namespace test\core\entities;


use DrlArchive\core\entities\AbstractResultEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\EventEntity;
use DrlArchive\core\entities\TeamEntity;
use PHPUnit\Framework\TestCase;

class DrlResultEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $entity = new DrlResultEntity();
        $this->assertInstanceOf(
            Entity::class,
            $entity
        );
        $this->assertInstanceOf(
            AbstractResultEntity::class,
            $entity
        );
    }

    public function testIdProperty(): void
    {
        $result = new DrlResultEntity();
        $result->setId(4);

        $this->assertEquals(
            4,
            $result->getId()
        );
    }

    public function testPositionProperty(): void
    {
        $result = new DrlResultEntity();
        $result->setPosition(3);
        $this->assertEquals(
            3,
            $result->getPosition()
        );
    }

    public function testPealNumberProperty(): void
    {
        $result = new DrlResultEntity();
        $result->setPealNumber(3);
        $this->assertEquals(
            3,
            $result->getPealNumber()
        );
    }

    public function testFaultsProperty(): void
    {
        $result = new DrlResultEntity();
        $result->setFaults(20.5);
        $this->assertEquals(
            20.5,
            $result->getFaults()
        );
    }

    public function testPointsProperty(): void
    {
        $result = new DrlResultEntity();
        $result->setPoints(20);
        $this->assertEquals(
            20,
            $result->getPoints()
        );
    }

    public function testTeamProperty(): void
    {
        $result = new DrlResultEntity();
        $result->setTeam(new TeamEntity());
        $this->assertInstanceOf(
            TeamEntity::class,
            $result->getTeam()
        );
    }

    public function testEventProperty(): void
    {
        $result = new DrlResultEntity();
        $result->setEvent(new EventEntity());
        $this->assertInstanceOf(
            EventEntity::class,
            $result->getEvent()
        );
    }
}


