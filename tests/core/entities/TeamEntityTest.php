<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

use DrlArchive\TestConstants;
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
        $team->setId(TestConstants::TEST_TEAM_ID);
        $this->assertEquals(
            TestConstants::TEST_TEAM_ID,
            $team->getId()
        );
    }

    public function testNameProperty(): void
    {
        $team = new TeamEntity();
        $team->setName(TestConstants::TEST_TEAM_NAME);
        $this->assertEquals(
            TestConstants::TEST_TEAM_NAME,
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
