<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\pages;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\pages\resultsArchive\ResultsArchive;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;

class ResultsArchiveFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new ResultsArchive();
        $useCase->setPresenter($presenter);

        return $useCase;
    }
}
