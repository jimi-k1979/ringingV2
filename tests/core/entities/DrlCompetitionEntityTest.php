<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;

class DrlCompetitionEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $entity = new DrlCompetitionEntity();
        $this->assertInstanceOf(
            AbstractCompetitionEntity::class,
            $entity
        );
        $this->assertInstanceOf(
            Entity::class,
            $entity
        );
    }

    public function testIdProperty(): void
    {
        $competition = new DrlCompetitionEntity();
        $competition->setId(TestConstants::TEST_DRL_COMPETITION_ID);
        $this->assertEquals(
            TestConstants::TEST_DRL_COMPETITION_ID,
            $competition->getId()
        );
    }

    public function testNameProperty(): void
    {
        $competition = new DrlCompetitionEntity();
        $competition->setName(TestConstants::TEST_DRL_COMPETITION_NAME);
        $this->assertEquals(
            TestConstants::TEST_DRL_COMPETITION_NAME,
            $competition->getName()
        );
    }

    public function testSingleTowerCompetitionProperty(): void
    {
        $competition = new DrlCompetitionEntity();
        $competition->setSingleTowerCompetition(
            TestConstants::TEST_DRL_SINGLE_TOWER_COMPETITION
        );
        $this->assertFalse(
            $competition->isSingleTowerCompetition()
        );
    }

    public function testUsualLocationProperty(): void
    {
        $competition = new DrlCompetitionEntity();
        $competition->setUsualLocation(new LocationEntity());
        $this->assertInstanceOf(
            LocationEntity::class,
            $competition->getUsualLocation()
        );
    }
}
