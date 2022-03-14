<?php

namespace DrlArchive\traits;

use DrlArchive\core\entities\RecordStatisticEntity;
use DrlArchive\TestConstants;

trait CreateMockStatisticsRecordTrait
{
    private function createMockStatisticsRecord(): RecordStatisticEntity
    {
        $entity = new RecordStatisticEntity();
        $entity->setId(TestConstants::TEST_RECORD_ID);
        $entity->setName(TestConstants::TEST_RECORD_NAME);
        $entity->setCategory(TestConstants::TEST_RECORD_CATEGORY);
        $entity->setShowOnIndex(TestConstants::TEST_SHOW_RECORD_ON_INDEX);

        return $entity;
    }
}
