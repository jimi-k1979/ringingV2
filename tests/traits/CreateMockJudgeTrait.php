<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\TestConstants;

trait CreateMockJudgeTrait
{
    private function createMockJudge(): JudgeEntity
    {
        $entity = new JudgeEntity();
        $entity->setId(TestConstants::TEST_JUDGE_ID);
        $entity->setFirstName(TestConstants::TEST_JUDGE_FIRST_NAME);
        $entity->setLastName(TestConstants::TEST_JUDGE_LAST_NAME);

        return $entity;
    }

}