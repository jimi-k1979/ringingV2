<?php

declare(strict_types=1);

namespace core\entities;

use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\entities\OtherCompetitionEntity;
use DrlArchive\core\entities\Entity;
use PHPUnit\Framework\TestCase;

class OtherCompetitionEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $entity = new OtherCompetitionEntity();
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
        $competition = new OtherCompetitionEntity();
        $competition->setId(4);
        $this->assertEquals(
            4,
            $competition->getId()
        );
    }

    public function testNameProperty(): void
    {
        $competition = new OtherCompetitionEntity();
        $competition->setName('Test');
        $this->assertEquals(
            'Test',
            $competition->getName()
        );
    }

    public function testSingleTowerCompetitionProperty(): void
    {
        $competition = new OtherCompetitionEntity();
        $competition->setSingleTowerCompetition(true);
        $this->assertTrue(
            $competition->isSingleTowerCompetition()
        );

        $competition->setSingleTowerCompetition(false);
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
