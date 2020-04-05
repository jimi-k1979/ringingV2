<?php

declare(strict_types=1);

namespace traits;


use DrlArchive\core\entities\RingerEntity;

trait CreateMockRingerTrait
{
    private function createMockRinger(): RingerEntity
    {
        $entity = new RingerEntity();
        $entity->setId(4321);
        $entity->setFirstName('Test');
        $entity->setLastName('Ringer');
        $entity->setNotes('Known as testicles');

        return $entity;
    }
}