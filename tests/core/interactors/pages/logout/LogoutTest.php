<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\logout;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use PHPUnit\Framework\TestCase;

class LogoutTest extends TestCase
{
    public function testRequestDefaults(): void
    {
        $request = new LogoutRequest();

        $this->assertEquals(
            '/index.php',
            $request->getForwardTo()
        );
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new Logout()
        );
    }

    public function testLogoutIsCalled(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasLogOutUserBeenCalled()
        );
    }

    private function createUseCase(): Logout
    {
        $request = new LogoutRequest();
        $request->setForwardTo('/testPage.php');

        $useCase = new Logout();
        $useCase->setRequest($request);
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setPresenter(new PresenterDummy());
        return $useCase;
    }

    public function testSendIsCalled(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $this->assertTrue(
            $presenterSpy->hasSendBeenCalled()
        );
    }

    public function testSuccessfulResponse(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_LOGGED_OUT,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                'forwardTo' => '/testPage.php',
            ],
            $response->getData()
        );
    }

    public function testFailureResponse(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setLogOutUserThrowsException();

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_TOO_MANY_REQUESTS,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                'forwardTo' => '/index.php',
                'message' => 'Something went wrong',
            ],
            $response->getData()
        );
        $this->assertEquals(
            '1305: Something went wrong',
            $response->getMessage()
        );
    }


}
