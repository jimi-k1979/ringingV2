<?php

namespace DrlArchive\implementation\factories\interactors\pages;

use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\pages\recordsPage\RecordsPage;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\AuthenticationManagerFactory;
use DrlArchive\implementation\factories\repositories\doctrine\PageDoctrineFactory;

class RecordsPageFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new RecordsPage();
        $useCase->setPresenter($presenter);
        $useCase->setAuthenticationManager(
            (new AuthenticationManagerFactory())->create()
        );
        $useCase->setPageRepository(
            (new PageDoctrineFactory())->create()
        );
        return $useCase;
    }
}
