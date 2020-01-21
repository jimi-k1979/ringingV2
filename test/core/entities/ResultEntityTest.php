<?php
declare(strict_types=1);

namespace test\core\entities;


use core\entities\ResultEntity;
use PHPUnit\Framework\TestCase;

class ResultEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            ResultEntity::class,
            new ResultEntity()
        );
    }
}

