<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\event;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\event\FetchDrlEventAndResults\FetchDrlEventAndResults;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\repositories\doctrine\EventDoctrineFactory;
use DrlArchive\implementation\factories\repositories\doctrine\JudgeDoctrineFactory;
use DrlArchive\implementation\factories\repositories\doctrine\LocationDoctrineFactory;
use DrlArchive\implementation\factories\repositories\doctrine\ResultDoctrineFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;
use DrlArchive\implementation\factories\repositories\UserRepositoryFactory;

class FetchDrlEventAndResultsFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new FetchDrlEventAndResults();

        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setUserRepository(
            (new UserRepositoryFactory())->create($loggedInUserId)
        );
        $useCase->setSecurityRepository(
            (new SecurityRepositoryFactory())->create()
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
        $useCase->setLocationRepository(
            (new LocationDoctrineFactory())->create()
        );

        return $useCase;
    }
}