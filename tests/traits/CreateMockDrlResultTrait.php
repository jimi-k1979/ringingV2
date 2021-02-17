<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\TestConstants;

trait CreateMockDrlResultTrait
{
    use CreateMockDrlEventTrait;

    public function createMockDrlResult(): DrlResultEntity
    {
        $team = new TeamEntity();
        $team->setId(TestConstants::TEST_TEAM_ID);
        $team->setName(TestConstants::TEST_TEAM_NAME);

        $entity = new DrlResultEntity();
        $entity->setId(TestConstants::TEST_RESULT_ID);
        $entity->setPosition(TestConstants::TEST_RESULT_POSITION);
        $entity->setFaults(TestConstants::TEST_RESULT_FAULTS);
        $entity->setPealNumber(TestConstants::TEST_RESULT_PEAL_NUMBER);
        $entity->setTeam($team);
        $entity->setEvent($this->createMockDrlEvent());
        $entity->setPoints(TestConstants::TEST_RESULT_POINTS);

        return $entity;
    }

    private function createMockEventResults(): array
    {
        $results = [];

        $team = new TeamEntity();

        $resultEntity = new DrlResultEntity();
        $resultEntity->setEvent($this->createMockDrlEvent());

        for ($i = 1; $i <= 4; $i++) {
            $team->setId($i);
            $team->setName("Team {$i}");

            $resultEntity->setId($i);
            $resultEntity->setPosition($i);
            $resultEntity->setFaults($i * 10.25);
            $resultEntity->setPealNumber(5 - $i);
            $resultEntity->setTeam(clone $team);
            $resultEntity->setPoints(8 - (2 * $i));

            $results[] = clone $resultEntity;
        }

        return $results;
    }
}