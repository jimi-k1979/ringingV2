<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\pages;

use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\pages\compositionPage\CompositionPage;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\AuthenticationManagerFactory;
use DrlArchive\implementation\factories\repositories\doctrine\CompositionDoctrineFactory;

class CompositionPageFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new CompositionPage();
        $useCase->setPresenter($presenter);
        $useCase->setAuthenticationManager(
            (new AuthenticationManagerFactory())->create()
        );
        $useCase->setCompositionRepository(
            (new CompositionDoctrineFactory())->create()
        );
        return $useCase;
    }
}
