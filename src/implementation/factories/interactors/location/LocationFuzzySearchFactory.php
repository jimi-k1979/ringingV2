<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\location;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interactors\location\locationFuzzySearch\LocationFuzzySearch;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\AuthenticationManagerFactory;
use DrlArchive\implementation\factories\repositories\doctrine\LocationDoctrineFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;

class LocationFuzzySearchFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new LocationFuzzySearch();

        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setLocationRepository(
            (new LocationDoctrineFactory())->create()
        );
        $useCase->setAuthenticationManager(
            (new AuthenticationManagerFactory())->create()
        );
        $useCase->setSecurityRepository(
            (new SecurityRepositoryFactory())->create()
        );

        return $useCase;
    }
}