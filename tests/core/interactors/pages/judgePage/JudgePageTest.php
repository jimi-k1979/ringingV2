<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\judgePage;

use DrlArchive\core\interactors\Interactor;
use PHPUnit\Framework\TestCase;

class JudgePageTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new JudgePage()
        );
    }

}
