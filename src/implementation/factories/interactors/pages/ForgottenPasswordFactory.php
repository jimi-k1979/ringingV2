<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors\pages;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interactors\pages\forgottenPassword\ForgottenPassword;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;
use DrlArchive\implementation\factories\managers\AuthenticationManagerFactory;
use DrlArchive\implementation\factories\managers\BasicEmailClientFactory;

class ForgottenPasswordFactory implements InteractorFactoryInterface
{

    public function create(
        PresenterInterface $presenter,
        ?Request $request = null,
        int $loggedInUserId = UserRepositoryInterface::GUEST_USER
    ): InteractorInterface {
        $useCase = new ForgottenPassword();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setAuthenticationManager(
            (new AuthenticationManagerFactory())->create()
        );
        $useCase->setEmailClient(
            (new BasicEmailClientFactory())->create()
        );

        return $useCase;
    }
}
