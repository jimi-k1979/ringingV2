<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new UserEntity()
        );
    }

    public function testIdProperty(): void
    {
        $entity = new UserEntity();
        $entity->setId(1);
        $this->assertEquals(
            1,
            $entity->getId()
        );
    }

    public function testUsernameProperty(): void
    {
        $entity = new UserEntity();
        $entity->setUsername('test');
        $this->assertEquals(
            'test',
            $entity->getUsername()
        );
    }

    public function testEmailAddressProperty(): void
    {
        $entity = new UserEntity();
        $entity->setEmailAddress('test@example.com');
        $this->assertEquals(
            'test@example.com',
            $entity->getEmailAddress()
        );
    }

    public function testPasswordProperty(): void
    {
        $entity = new UserEntity();
        $entity->setPassword('asdf');
        $this->assertEquals(
            'asdf',
            $entity->getPassword()
        );
    }

    public function testPermissionsProperty(): void
    {
        $entity = new UserEntity();
        $entity->setPermissions(
            [
                UserEntity::ADD_NEW_PERMISSION => true,
                UserEntity::EDIT_EXISTING_PERMISSION => true,
                UserEntity::APPROVE_EDIT_PERMISSION => true,
                UserEntity::CONFIRM_DELETE_PERMISSION => false,
            ]
        );
        $this->assertEquals(
            [
                UserEntity::ADD_NEW_PERMISSION => true,
                UserEntity::EDIT_EXISTING_PERMISSION => true,
                UserEntity::APPROVE_EDIT_PERMISSION => true,
                UserEntity::CONFIRM_DELETE_PERMISSION => false,
            ],
            $entity->getPermissions()
        );
    }

}
