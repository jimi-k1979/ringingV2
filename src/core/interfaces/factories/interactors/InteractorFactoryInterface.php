<?php
declare(strict_types=1);

namespace DrlArchive\core\factories\interactors;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;


interface InteractorFactoryInterface
{
    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface;
}