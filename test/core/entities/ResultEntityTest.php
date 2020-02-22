<?php
declare(strict_types=1);

namespace test\core\entities;


use core\entities\ResultEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\AbstractEventEntity;
use DrlArchive\core\entities\TeamEntity;
use PHPUnit\Framework\TestCase;

class ResultEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new ResultEntity()
        );
    }

    public function testIdProperty(): void
    {
        $result = new ResultEntity();
        $result->setId(4);

        $this->assertEquals(
            4,
            $result->getId()
        );
    }

    public function testPositionProperty(): void
    {
        $result = new ResultEntity();
        $result->setPosition(3);
        $this->assertEquals(
            3,
            $result->getPosition()
        );
    }

    public function testPealNumberProperty(): void
    {
        $result = new ResultEntity();
        $result->setPealNumber(3);
        $this->assertEquals(
            3,
            $result->getPealNumber()
        );
    }

    public function testFaultsProperty(): void
    {
        $result = new ResultEntity();
        $result->setFaults(20.5);
        $this->assertEquals(
            20.5,
            $result->getFaults()
        );
    }

    public function testPointsProperty(): void
    {
        $result = new ResultEntity();
        $result->setPoints(20);
        $this->assertEquals(
            20,
            $result->getPoints()
        );
    }

    public function testTeamProperty(): void
    {
        $result = new ResultEntity();
        $result->setTeam(new TeamEntity());
        $this->assertInstanceOf(
            TeamEntity::class,
            $result->getTeam()
        );
    }

    public function testEventProperty(): void
    {
        $result = new ResultEntity();
        $result->setEvent(new DrlEventEntity());
        $this->assertInstanceOf(
            AbstractEventEntity::class,
            $result->getEvent()
        );
    }
}


