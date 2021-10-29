<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\viewComposition;

use DrlArchive\core\interactors\Interactor;
use DrlArchive\traits\CreateMockUserTrait;
use PHPUnit\Framework\TestCase;

class ViewCompositionTest extends TestCase
{
    use CreateMockUserTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new ViewComposition()
        );
    }

    public function testRequestDefaults(): void
    {
        $request = new ViewCompositionRequest();

        $this->assertEquals(
            0,
            $request->getCompositionId()
        );
        $this->assertTrue(
            $request->isUpChanges()
        );
    }


}
