<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\pages;

use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\pages\ringerPage\RingerPage;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\AuthenticationManagerFactory;
use DrlArchive\implementation\factories\repositories\doctrine\RingerDoctrineFactory;
use DrlArchive\implementation\factories\repositories\SecurityRepositoryFactory;

class RingerPageFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $entity = new RingerPage();

        $entity->setPresenter($presenter);
        $entity->setRequest($request);
        $entity->setAuthenticationManager(
            (new AuthenticationManagerFactory())->create()
        );
        $entity->setSecurityRepository(
            (new SecurityRepositoryFactory())->create()
        );
        $entity->setRingerRepository(
            (new RingerDoctrineFactory())->create()
        );

        return $entity;
    }
}
