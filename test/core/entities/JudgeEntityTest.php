<?php
declare(strict_types=1);

namespace core\entities;

use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\entities\RingerEntity;
use PHPUnit\Framework\TestCase;

class JudgeEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new JudgeEntity()
        );
    }

    public function testIdProperty(): void
    {
        $judge = new JudgeEntity();
        $judge->setId(123);
        $this->assertEquals(
            123,
            $judge->getId()
        );
    }

    public function testFirstNameProperty(): void
    {
        $judge = new JudgeEntity();
        $judge->setFirstName('Test');
        $this->assertEquals(
            'Test',
            $judge->getFirstName()
        );
    }

    public function testLastNameProperty(): void
    {
        $judge = new JudgeEntity();
        $judge->setLastName('Test');
        $this->assertEquals(
            'Test',
            $judge->getLastName()
        );
    }

    public function testRingerProperty(): void
    {
        $judge = new JudgeEntity();
        $judge->setRinger(new RingerEntity());
        $this->assertInstanceOf(
            RingerEntity::class,
            $judge->getRinger()
        );
    }

    public function testGetFullName(): void
    {
        $judge = new JudgeEntity();
        $judge->setFirstName('Test');
        $judge->setLastName('Judge');
        $this->assertEquals(
            'Test Judge',
            $judge->getFirstName() . ' ' . $judge->getLastName()
        );
    }
}
