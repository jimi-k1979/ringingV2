<?php

declare(strict_types=1);

namespace DrlArchive\traits;

use DrlArchive\core\entities\ChangeEntity;
use DrlArchive\core\entities\CompositionEntity;
use DrlArchive\TestConstants;

trait CreateMockCompositionTrait
{
    private function createMockComposition(): CompositionEntity
    {
        $entity = new CompositionEntity();
        $entity->setId(
            TestConstants::TEST_COMPOSITION_ID
        );
        $entity->setName(
            TestConstants::TEST_COMPOSITION_NAME
        );
        $entity->setNumberOfBells(
            TestConstants::TEST_COMPOSITION_NUMBER_OF_BELLS
        );
        $entity->setTenorTurnedIn(
            TestConstants::TEST_COMPOSITION_TENOR_TURNED_IN
        );

        return $entity;
    }

    private function createMockChange(?int $changeNumber = null): ChangeEntity
    {
        $entity = new ChangeEntity();

        if ($changeNumber === null) {
            $entity->setId(
                TestConstants::TEST_CHANGE_ID
            );
            $entity->setChangeNumber(
                TestConstants::TEST_CHANGE_NUMBER
            );
        } else {
            $entity->setId(
                TestConstants::TEST_CHANGE_ID + $changeNumber
            );
            $entity->setChangeNumber($changeNumber);
        }
        $entity->setUpBell(
            TestConstants::TEST_CHANGE_BELL_UP
        );
        $entity->setDownBell(
            TestConstants::TEST_CHANGE_BELL_DOWN
        );
        $entity->setBellToFollow(
            TestConstants::TEST_CHANGE_BELL_TO_FOLLOW
        );

        return $entity;
    }
}
