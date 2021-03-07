<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\indexPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\traits\CreateMockUserTrait;
use PHPUnit\Framework\TestCase;

class IndexPageTest extends TestCase
{
    use CreateMockUserTrait;

    public function testRequestDefaults(): void
    {
        $request = new IndexPageRequest();
        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $request->getPreviousStatus()
        );
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new IndexPage()
        );
    }

    public function testFindOutIfUserIsLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasIsLoggedInBeenCalled()
        );
    }

    private function createUseCase(): IndexPage
    {
        $useCase = new IndexPage();
        $useCase->setRequest(new IndexPageRequest());
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());

        return $useCase;
    }

    public function testGetUserDetailsIfLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasLoggedInUserDetailsBeenCalled()
        );
    }

    public function testDontGetUserDetailsIfNotLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setIsLoggedInToFalse();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertFalse(
            $authenticationSpy->hasLoggedInUserDetailsBeenCalled()
        );
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

    public function testLoggedInResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setLoggedInUserDetailsValue(
            $this->createMockSuperAdmin()
        );

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                'previousStatus' => Response::STATUS_SUCCESS,
            ],
            $response->getData()
        );
        $this->assertEquals(
            $this->createMockSuperAdmin(),
            $response->getLoggedInUser()
        );
    }
    
    

}
