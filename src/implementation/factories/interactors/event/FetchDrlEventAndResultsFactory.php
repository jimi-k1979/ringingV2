<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\event;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\event\FetchDrlEventAndResults\FetchDrlEventAndResults;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\repositories\EventRepositoryFactory;
use DrlArchive\implementation\factories\repositories\JudgeRepositoryFactory;
use DrlArchive\implementation\factories\repositories\LocationRepositoryFactory;
use DrlArchive\implementation\factories\repositories\ResultRepositoryFactory;
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
            (new EventRepositoryFactory())->create()
        );
        $useCase->setResultRepository(
            (new ResultRepositoryFactory())->create()
        );
        $useCase->setJudgeRepository(
            (new JudgeRepositoryFactory())->create()
        );
        $useCase->setLocationRepository(
            (new LocationRepositoryFactory())->create()
        );

        return $useCase;
    }
}