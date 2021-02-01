<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\TestConstants;
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
        $judge->setId(TestConstants::TEST_JUDGE_ID);
        $this->assertEquals(
            TestConstants::TEST_JUDGE_ID,
            $judge->getId()
        );
    }

    public function testFirstNameProperty(): void
    {
        $judge = new JudgeEntity();
        $judge->setFirstName(TestConstants::TEST_JUDGE_FIRST_NAME);
        $this->assertEquals(
            TestConstants::TEST_JUDGE_FIRST_NAME,
            $judge->getFirstName()
        );
    }

    public function testLastNameProperty(): void
    {
        $judge = new JudgeEntity();
        $judge->setLastName(TestConstants::TEST_JUDGE_LAST_NAME);
        $this->assertEquals(
            TestConstants::TEST_JUDGE_LAST_NAME,
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
        $judge->setFirstName(TestConstants::TEST_JUDGE_FIRST_NAME);
        $judge->setLastName(TestConstants::TEST_JUDGE_LAST_NAME);
        $this->assertEquals(
            TestConstants::TEST_JUDGE_FIRST_NAME . ' ' . TestConstants::TEST_JUDGE_LAST_NAME,
            $judge->getFullName()
        );
    }
}
