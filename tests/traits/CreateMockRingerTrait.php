<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\entities\WinningRingerEntity;
use DrlArchive\TestConstants;

trait CreateMockRingerTrait
{
    private function createMockRinger(): RingerEntity
    {
        $entity = new RingerEntity();
        $entity->setId(TestConstants::TEST_RINGER_ID);
        $entity->setFirstName(TestConstants::TEST_RINGER_FIRST_NAME);
        $entity->setLastName(TestConstants::TEST_RINGER_LAST_NAME);
        $entity->setNotes(TestConstants::TEST_RINGER_NOTES);
        $entity->setJudgeId(TestConstants::TEST_JUDGE_ID);

        return $entity;
    }

    private function createMockWinningRinger(): WinningRingerEntity
    {
        $entity = new WinningRingerEntity();
        $entity->setId(TestConstants::TEST_WINNING_RINGER_ID);
        $entity->setRinger($this->createMockRinger());
        $entity->setBell(TestConstants::TEST_WINNING_RINGER_BELL);

        return $entity;
    }
}
