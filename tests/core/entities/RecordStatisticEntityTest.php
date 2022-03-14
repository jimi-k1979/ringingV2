<?php

namespace DrlArchive\core\entities;

use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;

class RecordStatisticEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new RecordStatisticEntity()
        );
    }

    public function testNameProperty(): void
    {
        $entity = new RecordStatisticEntity();

        $this->assertNull(
            $entity->getName()
        );

        $entity->setName(TestConstants::TEST_RECORD_NAME);

        $this->assertEquals(
            TestConstants::TEST_RECORD_NAME,
            $entity->getName()
        );
    }

    public function testCategoryProperty(): void
    {
        $entity = new RecordStatisticEntity();

        $this->assertNull(
            $entity->getCategory()
        );

        $entity->setCategory(TestConstants::TEST_RECORD_CATEGORY);

        $this->assertEquals(
            TestConstants::TEST_RECORD_CATEGORY,
            $entity->getCategory()
        );
    }

    public function testShowIndexProperty(): void
    {
        $entity = new RecordStatisticEntity();

        $this->assertFalse(
            $entity->isShowOnIndex()
        );

        $entity->setShowOnIndex(TestConstants::TEST_SHOW_RECORD_ON_INDEX);
        $this->assertTrue(
            $entity->isShowOnIndex()
        );
    }


}
