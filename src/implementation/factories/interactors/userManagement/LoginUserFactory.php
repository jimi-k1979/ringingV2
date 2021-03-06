<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\userManagement;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\userManagement\loginUser\LoginUser;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\AuthenticationManagerFactory;

class LoginUserFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new LoginUser();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setAuthenticationManager(
            (new AuthenticationManagerFactory())->create()
        );

        return $useCase;
    }
}
