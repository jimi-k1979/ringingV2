<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\team;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\team\TeamFuzzySearch\TeamFuzzySearch;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\AuthenticationManagerFactory;
use DrlArchive\implementation\factories\repositories\doctrine\TeamDoctrineFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;
use DrlArchive\implementation\factories\repositories\UserRepositoryFactory;

class TeamFuzzySearchFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new TeamFuzzySearch();

        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setSecurityRepository(
            (new SecurityRepositoryFactory())->create()
        );
        $useCase->setAuthenticationManager(
            (new AuthenticationManagerFactory())->create()
        );
        $useCase->setTeamRepository(
            (new TeamDoctrineFactory())->create()
        );

        return $useCase;
    }
}
