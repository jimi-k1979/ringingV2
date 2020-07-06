<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\event;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\event\checkDrlEventExists\CheckDrlEventExists;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\repositories\CompetitionRepositoryFactory;
use DrlArchive\implementation\factories\repositories\EventRepositoryFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;
use DrlArchive\implementation\factories\repositories\UserRepositoryFactory;

class CheckDrlEventExistsFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new CheckDrlEventExists();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setUserRepository(
            (new UserRepositoryFactory())->create($loggedInUserId)
        );
        $useCase->setSecurityRepository(
            (new SecurityRepositoryFactory())->create()
        );
        $useCase->setEventRepository(
            (new EventRepositoryFactory())->create()
        );
        $useCase->setCompetitionRepository(
            (new CompetitionRepositoryFactory())->create()
        );

        return $useCase;
    }
}