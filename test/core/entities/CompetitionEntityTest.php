<?php
declare(strict_types=1);

namespace core\entities;

use DrlArchive\core\entities\CompetitionEntity;
use DrlArchive\core\entities\Entity;
use PHPUnit\Framework\TestCase;

class CompetitionEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new CompetitionEntity()
        );
    }

    public function testIdProperty(): void
    {
        $competition = new CompetitionEntity();
        $competition->setId(4);
        $this->assertEquals(
            4,
            $competition->getId()
        );
    }

    public function testNameProperty(): void
    {
        $competition = new CompetitionEntity();
        $competition->setName('Test');
        $this->assertEquals(
            'Test',
            $competition->getName()
        );
    }

    public function testSingleTowerCompetitionProperty(): void
    {
        $competition = new CompetitionEntity();
        $competition->setSingleTowerCompetition(true);
        $this->assertTrue(
            $competition->isSingleTowerCompetition()
        );

        $competition->setSingleTowerCompetition(false);
        $this->assertFalse(
            $competition->isSingleTowerCompetition()
        );
    }
}
