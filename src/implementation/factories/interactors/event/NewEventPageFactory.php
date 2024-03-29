<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\event;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\event\newEventPage\NewEventPage;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\AuthenticationManagerFactory;
use DrlArchive\implementation\factories\managers\DoctrineTransactionManagerFactory;
use DrlArchive\implementation\factories\repositories\doctrine\EventDoctrineFactory;
use DrlArchive\implementation\factories\repositories\doctrine\RingerDoctrineFactory;
use DrlArchive\implementation\factories\repositories\doctrine\TeamDoctrineFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;

class NewEventPageFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new NewEventPage();
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
        $useCase->setEventRepository(
            (new EventDoctrineFactory())->create()
        );
        $useCase->setResultRepository(
            (new RingerDoctrineFactory())->create()
        );
        $useCase->setTransactionManager(
            (new DoctrineTransactionManagerFactory())->create()
        );

        return $useCase;
    }
}
