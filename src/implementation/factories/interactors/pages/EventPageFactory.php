<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\pages;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\pages\eventPage\EventPage;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\AuthenticationManagerFactory;
use DrlArchive\implementation\factories\repositories\doctrine\EventDoctrineFactory;
use DrlArchive\implementation\factories\repositories\doctrine\JudgeDoctrineFactory;
use DrlArchive\implementation\factories\repositories\doctrine\ResultDoctrineFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;

class EventPageFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new EventPage();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setSecurityRepository(
            (new SecurityRepositoryFactory())->create()
        );
        $useCase->setAuthenticationManager(
            (new AuthenticationManagerFactory())->create()
        );
        $useCase->setEventRepository(
            (new EventDoctrineFactory())->create()
        );
        $useCase->setResultRepository(
            (new ResultDoctrineFactory())->create()
        );
        $useCase->setJudgeRepository(
            (new JudgeDoctrineFactory())->create()
        );

        return $useCase;
    }
}
