<?php
declare(strict_types=1);

namespace core\entities;

use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\AbstractEventEntity;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\entities\WinningRingerEntity;
use PHPUnit\Framework\TestCase;

class WinningRingerEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new WinningRingerEntity()
        );
    }

    public function testIdProperty(): void
    {
        $winner = new WinningRingerEntity();
        $winner->setId(4);
        $this->assertEquals(
            4,
            $winner->getId()
        );
    }

    public function testBellProperty(): void
    {
        $winner = new WinningRingerEntity();
        $winner->setBell('4');
        $this->assertEquals(
            4,
            $winner->getBell()
        );
    }

    public function testRingerProperty(): void
    {
        $winner = new WinningRingerEntity();
        $winner->setRinger(new RingerEntity());
        $this->assertInstanceOf(
            RingerEntity::class,
            $winner->getRinger()
        );
    }

    public function testEventProperty(): void
    {
        $winner = new WinningRingerEntity();
        $winner->setEvent(new DrlEventEntity());
        $this->assertInstanceOf(
            AbstractEventEntity::class,
            $winner->getEvent()
        );
    }

}
