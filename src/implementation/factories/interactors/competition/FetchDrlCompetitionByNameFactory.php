<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\competition;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\competition\fetchDrlCompetitionByName\FetchDrlCompetitionByName;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\repositories\CompetitionRepositoryFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;
use DrlArchive\implementation\factories\repositories\UserRepositoryFactory;

class FetchDrlCompetitionByNameFactory implements
    InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $entity = new FetchDrlCompetitionByName();
        $entity->setRequest($request);
        $entity->setPresenter($presenter);
        $entity->setSecurityRepository(
            (new SecurityRepositoryFactory())->create()
        );
        $entity->setUserRepository(
            (new UserRepositoryFactory())->create($loggedInUserId)
        );
        $entity->setCompetitionRepository(
            (new CompetitionRepositoryFactory())->create()
        );

        return $entity;
    }
}
