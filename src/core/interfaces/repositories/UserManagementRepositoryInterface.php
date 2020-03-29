<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\UserEntity;

interface UserManagementRepositoryInterface
{
    const UNABLE_TO_INSERT_EXCEPTION = 2801;
    const NO_ROWS_FOUND_EXCEPTION = 2802;
    const NO_ROWS_UPDATED_EXCEPTION = 2803;
    const NO_ROWS_DELETED_EXCEPTION = 2804;

    public function fetchById(int $userId): UserEntity;
}