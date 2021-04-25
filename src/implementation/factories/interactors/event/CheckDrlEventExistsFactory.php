<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\event;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\event\checkDrlEventExists\CheckDrlEventExists;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\AuthenticationManagerFactory;
use DrlArchive\implementation\factories\repositories\doctrine\CompetitionDoctrineFactory;
use DrlArchive\implementation\factories\repositories\doctrine\EventDoctrineFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;

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
        $useCase->setAuthenticationManager(
            (new AuthenticationManagerFactory())->create()
        );
        $useCase->setSecurityRepository(
            (new SecurityRepositoryFactory())->create()
        );
        $useCase->setEventRepository(
            (new EventDoctrineFactory())->create()
        );
        $useCase->setCompetitionRepository(
            (new CompetitionDoctrineFactory())->create()
        );

        return $useCase;
    }
}
