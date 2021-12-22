<?php

namespace DrlArchive\core\interactors\pages\teamPage;

use DrlArchive\core\interactors\Interactor;
use PHPUnit\Framework\TestCase;

class TeamPageTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new TeamPage()
        );
    }


}
