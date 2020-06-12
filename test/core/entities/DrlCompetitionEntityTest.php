<?php
declare(strict_types=1);

namespace core\entities;

use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\LocationEntity;
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
        $competition->setId(4);
        $this->assertEquals(
            4,
            $competition->getId()
        );
    }

    public function testNameProperty(): void
    {
        $competition = new DrlCompetitionEntity();
        $competition->setName('Test');
        $this->assertEquals(
            'Test',
            $competition->getName()
        );
    }

    public function testSingleTowerCompetitionProperty(): void
    {
        $competition = new DrlCompetitionEntity();
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
