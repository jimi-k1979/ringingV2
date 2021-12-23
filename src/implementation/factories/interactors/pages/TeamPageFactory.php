<?php

namespace DrlArchive\implementation\factories\interactors\pages;

use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\pages\teamPage\TeamPage;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\AuthenticationManagerFactory;
use DrlArchive\implementation\factories\repositories\doctrine\TeamDoctrineFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;

class TeamPageFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new TeamPage();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setAuthenticationManager(
            (new AuthenticationManagerFactory())->create()
        );
        $useCase->setSecurityRepository(
            (new SecurityRepositoryFactory())->create()
        );
        $useCase->setTeamRepository(
            (new TeamDoctrineFactory())->create()
        );

        return $useCase;
    }
}
