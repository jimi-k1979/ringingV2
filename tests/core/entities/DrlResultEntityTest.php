<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;


use DrlArchive\core\entities\AbstractResultEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\AbstractEventEntity;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\TestConstants;
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
        $result->setId(TestConstants::TEST_RESULT_ID);

        $this->assertEquals(
            TestConstants::TEST_RESULT_ID,
            $result->getId()
        );
    }

    public function testPositionProperty(): void
    {
        $result = new DrlResultEntity();
        $result->setPosition(TestConstants::TEST_RESULT_POSITION);
        $this->assertEquals(
            TestConstants::TEST_RESULT_POSITION,
            $result->getPosition()
        );
    }

    public function testPealNumberProperty(): void
    {
        $result = new DrlResultEntity();
        $result->setPealNumber(TestConstants::TEST_RESULT_PEAL_NUMBER);
        $this->assertEquals(
            TestConstants::TEST_RESULT_PEAL_NUMBER,
            $result->getPealNumber()
        );
    }

    public function testFaultsProperty(): void
    {
        $result = new DrlResultEntity();
        $result->setFaults(TestConstants::TEST_RESULT_FAULTS);
        $this->assertEquals(
            TestConstants::TEST_RESULT_FAULTS,
            $result->getFaults()
        );
    }

    public function testPointsProperty(): void
    {
        $result = new DrlResultEntity();
        $result->setPoints(TestConstants::TEST_RESULT_POINTS);
        $this->assertEquals(
            TestConstants::TEST_RESULT_POINTS,
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
        $result->setEvent(new DrlEventEntity());
        $this->assertInstanceOf(
            AbstractEventEntity::class,
            $result->getEvent()
        );
    }
}
