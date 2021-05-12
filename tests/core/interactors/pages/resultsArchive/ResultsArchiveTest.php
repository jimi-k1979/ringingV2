<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\resultsArchive;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\traits\CreateMockUserTrait;
use PHPUnit\Framework\TestCase;

class ResultsArchiveTest extends TestCase
{
    use CreateMockUserTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new ResultsArchive()
        );
    }

    public function testFindOutIfUserIsLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setLoggedInUserDetailsValue(
            $this->createMockSuperAdmin()
        );

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasIsLoggedInBeenCalled()
        );
    }

    private function createUseCase(): ResultsArchive
    {
        $useCase = new ResultsArchive();
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

    public function testGetUserDetailsNotCalledIfNotLoggedIn(): void
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
        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $this->assertTrue(
            $presenter->hasSendBeenCalled()
        );
    }

    public function testNotLoggedInResponse(): void
    {
        $presenter = new PresenterSpy();
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setIsLoggedInToFalse();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEmpty(
            $response->getData()
        );
        $this->assertNull(
            $response->getLoggedInUser()->getId()
        );
    }

    public function testLoggedInResponse(): void
    {
        $presenter = new PresenterSpy();
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setLoggedInUserDetailsValue(
            $this->createMockSuperAdmin()
        );

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEmpty(
            $response->getData()
        );
        $this->assertEquals(
            $this->createMockSuperAdmin(),
            $response->getLoggedInUser()
        );
    }

}
