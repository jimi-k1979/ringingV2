<?php
declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\team;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interactors\team\CreateTeam\CreateTeam;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\DoctrineTransactionManagerFactory;
use DrlArchive\implementation\factories\managers\TransactionManagerFactory;
use DrlArchive\implementation\factories\repositories\DeaneryRepositoryFactory;
use DrlArchive\implementation\factories\repositories\doctrine\DeaneryDoctrineFactory;
use DrlArchive\implementation\factories\repositories\doctrine\TeamDoctrineFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;
use DrlArchive\implementation\factories\repositories\UserRepositoryFactory;

class CreateTeamFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new CreateTeam();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setUserRepository(
            (new UserRepositoryFactory())->create()
        );
        $useCase->setSecurityRepository(
            (new SecurityRepositoryFactory())->create()
        );
        $useCase->setTeamRepository(
            (new TeamDoctrineFactory())->create()
        );
        $useCase->setDeaneryRepository(
            (new DeaneryDoctrineFactory())->create()
        );
        $useCase->setTransactionManager(
            (new DoctrineTransactionManagerFactory())->create()
        );

        return $useCase;
    }
}
