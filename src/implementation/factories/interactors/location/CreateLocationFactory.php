<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\location;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interactors\location\createLocation\CreateLocation;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\DoctrineTransactionManagerFactory;
use DrlArchive\implementation\factories\repositories\doctrine\DeaneryDoctrineFactory;
use DrlArchive\implementation\factories\repositories\doctrine\LocationDoctrineFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;
use DrlArchive\implementation\factories\repositories\UserRepositoryFactory;

class CreateLocationFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new CreateLocation();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setUserRepository(
            (new UserRepositoryFactory())->create($loggedInUserId)
        );
        $useCase->setSecurityRepository(
            (new SecurityRepositoryFactory())->create()
        );
        $useCase->setDeaneryRepository(
            (new DeaneryDoctrineFactory())->create()
        );
        $useCase->setLocationRepository(
            (new LocationDoctrineFactory())->create()
        );
        $useCase->setTransactionManager(
            (new DoctrineTransactionManagerFactory())->create()
        );

        return $useCase;
    }
}