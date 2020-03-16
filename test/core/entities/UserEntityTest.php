<?php

declare(strict_types=1);

namespace core\entities;

use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\UserEntity;
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

    public function testLoginCountProperty(): void
    {
        $entity = new UserEntity();
        $entity->setLoginCount(1);
        $this->assertEquals(
            1,
            $entity->getLoginCount()
        );
    }

    public function testPermissionsProperty(): void
    {
        $entity = new UserEntity();
        $entity->setPermissions(
            [
                'addNew' => true,
                'editExisting' => true,
                'approveEdit' => true,
            ]
        );
        $this->assertEquals(
            [
                'addNew' => true,
                'editExisting' => true,
                'approveEdit' => true,
            ],
            $entity->getPermissions()
        );
    }

}
