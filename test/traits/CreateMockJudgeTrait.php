<?php

declare(strict_types=1);

namespace traits;


use DrlArchive\core\entities\JudgeEntity;

trait CreateMockJudgeTrait
{
    private function createMockJudge(): JudgeEntity
    {
        $entity = new JudgeEntity();
        $entity->setId(4321);
        $entity->setFirstName('Test');
        $entity->setLastName('Judge');

        return $entity;
    }

}