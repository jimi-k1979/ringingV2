<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\UserEntity;

interface SecurityRepositoryInterface
{
    public const GENERAL_EXCEPTION_CODE = 2700;

    public const ADD_NEW_PERMISSION = 'addNew';
    public const EDIT_EXISTING_PERMISSION = 'editExisting';
    public const APPROVE_EDIT_PERMISSION = 'approveEdit';
    public const DELETE_PERMISSION = 'delete';

    public function isUserAuthorised(
        UserEntity $user,
        ?string $permission = null
    ): bool;
}