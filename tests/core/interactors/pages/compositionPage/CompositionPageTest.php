<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\compositionPage;

use PHPUnit\Framework\TestCase;

class CompositionPageTest extends TestCase
{

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new CompositionPage()
        );
    }

}
