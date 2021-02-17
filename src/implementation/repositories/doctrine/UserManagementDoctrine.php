<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\core\interfaces\repositories\UserManagementRepositoryInterface;
use Throwable;

class UserManagementDoctrine extends DoctrineRepository implements
    UserManagementRepositoryInterface
{

    private const FIELD_USER_ID = 'u.id';
    private const FIELD_USERNAME = 'u.username';
    private const FIELD_EMAIL_ADDRESS = 'u.emailAddress';
    private const FIELD_PASSWORD = 'u.password';
    private const FIELD_LOGIN_COUNT = 'u.loginCount';
    private const FIELD_ADD_NEW_PERMISSION = 'up.addNew';
    private const FIELD_EDIT_EXISTING_PERMISSION = 'up.editExisting';
    private const FIELD_APPROVE_EDIT_PERMISSION = 'up.approveEdit';
    private const FIELD_PERMISSION_CONFIRM_DELETE = 'up.confirmDelete';

    /**
     * @param int $userId
     * @return UserEntity
     * @throws CleanArchitectureException
     */
    public function fetchById(int $userId): UserEntity
    {
        try {
            $query = $this->database->createQueryBuilder();
            $query->select(
                [
                    self::FIELD_USER_ID . ' AS ' . Repository::ALIAS_USER_ID,
                    self::FIELD_USERNAME . ' AS ' . Repository::ALIAS_USERNAME,
                    self::FIELD_EMAIL_ADDRESS . ' AS ' . Repository::ALIAS_EMAIL_ADDRESS,
                    self::FIELD_PASSWORD . ' AS ' . Repository::ALIAS_PASSWORD,
                    self::FIELD_LOGIN_COUNT . ' AS ' . Repository::ALIAS_LOGIN_COUNT,
                    self::FIELD_ADD_NEW_PERMISSION . ' AS ' . Repository::ALIAS_ADD_NEW,
                    self::FIELD_EDIT_EXISTING_PERMISSION . ' AS ' . Repository::ALIAS_EDIT_EXISTING,
                    self::FIELD_APPROVE_EDIT_PERMISSION . ' AS ' . Repository::ALIAS_APPROVE_EDIT,
                    self::FIELD_PERMISSION_CONFIRM_DELETE . ' AS ' . Repository::ALIAS_CONFIRM_DELETE,
                ]
            )
                ->from('user', 'u')
                ->leftJoin(
                    'u',
                    'user_permissions',
                    'up',
                    'u.permissionId = up.id'
                )
                ->where(
                    $query->expr()->eq(self::FIELD_USER_ID, ':id')
                )
                ->setParameter('id', $userId);
            $result = $query->execute()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No user found - connection error',
                UserManagementRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No user found',
                UserManagementRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateUserEntity($result);
    }

    private function generateUserEntity(array $row): UserEntity
    {
        $entity = new UserEntity();

        if (isset($row[Repository::ALIAS_USER_ID])) {
            $entity->setId((int)$row[Repository::ALIAS_USER_ID]);
        }
        if (isset($row[Repository::ALIAS_USERNAME])) {
            $entity->setUsername($row[Repository::ALIAS_USERNAME]);
        }
        if (isset($row[Repository::ALIAS_EMAIL_ADDRESS])) {
            $entity->setEmailAddress($row[Repository::ALIAS_EMAIL_ADDRESS]);
        }
        if (isset($row[Repository::ALIAS_PASSWORD])) {
            $entity->setPassword($row[Repository::ALIAS_PASSWORD]);
        }
        if (isset($row[Repository::ALIAS_LOGIN_COUNT])) {
            $entity->setLoginCount((int)$row[Repository::ALIAS_LOGIN_COUNT]);
        }
        $entity->setPermissions(
            [
                UserEntity::ADD_NEW_PERMISSION =>
                    (bool)$row[Repository::ALIAS_ADD_NEW] ?? false,
                UserEntity::EDIT_EXISTING_PERMISSION =>
                    (bool)$row[Repository::ALIAS_EDIT_EXISTING] ?? false,
                UserEntity::APPROVE_EDIT_PERMISSION =>
                    (bool)$row[Repository::ALIAS_APPROVE_EDIT] ?? false,
                UserEntity::CONFIRM_DELETE_PERMISSION =>
                    (bool)$row[Repository::ALIAS_CONFIRM_DELETE] ?? false,
            ]
        );

        return $entity;
    }
}
