<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\compositionPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\CompositionDummy;
use DrlArchive\mocks\CompositionSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockUserTrait;
use PHPUnit\Framework\TestCase;

class CompositionPageTest extends TestCase
{
    use CreateMockUserTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new CompositionPage()
        );
    }

    public function testIsUserLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasIsLoggedInBeenCalled()
        );
    }

    private function createUseCase(): CompositionPage
    {
        $useCase = new CompositionPage();
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setCompositionRepository(new CompositionDummy());

        return $useCase;
    }

    public function testGetUserIfLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasLoggedInUserDetailsBeenCalled()
        );
    }

    public function testDontGetUserIfNotLoggedIn(): void
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

    public function testCompositionsAreFetched(): void
    {
        $compositionSpy = new CompositionSpy();

        $useCase = $this->createUseCase();
        $useCase->setCompositionRepository($compositionSpy);
        $useCase->execute();

        $this->assertTrue(
            $compositionSpy->hasFetchAllCompositionsBeenCalled()
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

    public function testResponseNotLoggedIn(): void
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
        $this->assertEquals(
            $this->standardResponseData(),
            $response->getData()
        );
        $this->assertNull(
            $response->getLoggedInUser()->getId()
        );
    }

    private function standardResponseData(): array
    {
        return [
            [
                'compositionId' =>
                    TestConstants::TEST_COMPOSITION_ID,
                'composition' =>
                    TestConstants::TEST_COMPOSITION_NAME,
                'numberOfBells' =>
                    TestConstants::TEST_COMPOSITION_NUMBER_OF_BELLS,
                'tenorTurnedIn' => 'false',
                'description' => null,
                'length' => 'short',
            ]
        ];
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
            $this->standardResponseData(),
            $response->getData()
        );
        $this->assertEquals(
            $this->createMockSuperAdmin(),
            $response->getLoggedInUser()
        );
    }

}
