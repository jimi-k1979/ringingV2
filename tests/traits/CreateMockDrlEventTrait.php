<?php

declare(strict_types=1);

namespace DrlArchive\traits;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\TestConstants;

trait CreateMockDrlEventTrait
{
    use CreateMockLocationTrait;
    use CreateMockDrlCompetitionTrait;

    private function createMockDrlEvent(): DrlEventEntity
    {
        $entity = new DrlEventEntity();
        $entity->setId(TestConstants::TEST_EVENT_ID);
        $entity->setLocation($this->createMockLocation());
        $entity->setCompetition($this->createMockDrlCompetition());
        $entity->setYear(TestConstants::TEST_EVENT_YEAR);

        return $entity;
    }

}