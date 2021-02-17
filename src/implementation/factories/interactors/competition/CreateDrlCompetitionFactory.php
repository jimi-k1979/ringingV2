<?php
declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\competition;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interactors\competition\createDrlCompetition\CreateDrlCompetition;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\DoctrineTransactionManagerFactory;
use DrlArchive\implementation\factories\repositories\doctrine\CompetitionDoctrineFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;
use DrlArchive\implementation\factories\repositories\UserRepositoryFactory;

class CreateDrlCompetitionFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new CreateDrlCompetition();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setUserRepository(
            (new UserRepositoryFactory())->create($loggedInUserId)
        );
        $useCase->setSecurityRepository(
            (new SecurityRepositoryFactory())->create()
        );
        $useCase->setCompetitionRepository(
            (new CompetitionDoctrineFactory())->create()
        );
        $useCase->setTransactionManager(
            (new DoctrineTransactionManagerFactory())->create()
        );
        return $useCase;
    }
}
