<?php

declare(strict_types=1);

namespace core\entities;

use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\TeamEntity;
use PHPUnit\Framework\TestCase;

class TeamEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new TeamEntity()
        );
    }

    public function testIdProperty(): void
    {
        $team = new TeamEntity();
        $team->setId(4);
        $this->assertEquals(
            4,
            $team->getId()
        );
    }

    public function testNameProperty(): void
    {
        $team = new TeamEntity();
        $team->setName('Test');
        $this->assertEquals(
            'Test',
            $team->getName()
        );
    }

    public function testDeaneryProperty(): void
    {
        $team = new TeamEntity();
        $team->setDeanery(new DeaneryEntity());
        $this->assertInstanceOf(
            DeaneryEntity::class,
            $team->getDeanery()
        );
    }
}
