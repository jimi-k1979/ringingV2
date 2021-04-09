<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\userManagement\logoutUser;

use DrlArchive\core\classes\Response;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use PHPUnit\Framework\TestCase;

class LogoutUserTest extends TestCase
{
    public function testRequestDefaults(): void
    {
        $request = new LogoutUserRequest();

        $this->assertEquals(
            '/index.php',
            $request->getRedirectTo()
        );
    }

    public function testLogoutUserCalled(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasLogOutUserBeenCalled()
        );
    }

    private function createUseCase(): LogoutUser
    {
        $useCase = new LogoutUser();
        $useCase->setRequest(new LogoutUserRequest());
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());

        return $useCase;
    }

    public function testHasSendBeenCalled(): void
    {
        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $this->assertTrue(
            $presenter->hasSendBeenCalled()
        );
    }

    public function testDefaultResponse(): void
    {
        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                LogoutUser::DATA_FIELD_REDIRECT_TO => '/index.php',
            ],
            $response->getData()
        );
    }

}
