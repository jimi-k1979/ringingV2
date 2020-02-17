<?php
declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors;


use DrlArchive\core\classes\Request;
use DrlArchive\core\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interactors\location\createLocation\CreateLocation;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\implementation\factories\managers\TransactionManagerFactory;
use DrlArchive\implementation\factories\repositories\DeaneryRepositoryFactory;
use DrlArchive\implementation\factories\repositories\LocationRepositoryFactory;

class CreateLocationFactory implements InteractorFactoryInterface
{

    public function create(PresenterInterface $presenter, ?Request $request = null): InteractorInterface
    {
        $useCase = new CreateLocation();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setDeaneryRepository(
            (new DeaneryRepositoryFactory())->create()
        );
        $useCase->setLocationRepository(
            (new LocationRepositoryFactory())->create()
        );
        $useCase->setTransactionManager(
            (new TransactionManagerFactory())->create()
        );

        return $useCase;
    }
}