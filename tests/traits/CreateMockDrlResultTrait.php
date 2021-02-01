<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\entities\TeamEntity;

trait CreateMockDrlResultTrait
{
    use CreateMockDrlEventTrait;

    public function createMockDrlResult(): DrlResultEntity
    {
        $team = new TeamEntity();
        $team->setId(1);
        $team->setName('First Place');

        $entity = new DrlResultEntity();
        $entity->setId(123);
        $entity->setPosition(1);
        $entity->setFaults(10.25);
        $entity->setPealNumber(1);
        $entity->setTeam($team);
        $entity->setEvent($this->createMockDrlEvent());
        $entity->setPoints(10);

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