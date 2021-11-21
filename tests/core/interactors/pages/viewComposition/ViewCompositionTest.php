<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\viewComposition;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\CompositionDummy;
use DrlArchive\mocks\CompositionSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockCompositionTrait;
use DrlArchive\traits\CreateMockUserTrait;
use PHPUnit\Framework\TestCase;

class ViewCompositionTest extends TestCase
{
    use CreateMockUserTrait;
    use CreateMockCompositionTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new ViewComposition()
        );
    }

    public function testRequestDefaults(): void
    {
        $request = new ViewCompositionRequest();

        $this->assertEquals(
            0,
            $request->getCompositionId()
        );
        $this->assertTrue(
            $request->isUpChanges()
        );
    }

    public function testUserLoggedInCheck(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasLoggedInUserDetailsBeenCalled()
        );
    }

    private function createUseCase(): ViewComposition
    {
        $request = new ViewCompositionRequest();
        $request->setCompositionId(TestConstants::TEST_COMPOSITION_ID);

        $useCase = new ViewComposition();
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setRequest($request);
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

    public function testFailureOnNoCompositionIdInRequest(): void
    {
        $presenter = new PresenterSpy();
        $request = new ViewCompositionRequest();

        $useCase = $this->createUseCase();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_BAD_REQUEST,
            $response->getStatus()
        );
        $this->assertEquals(
            'No composition id given',
            $response->getMessage()
        );
    }

    public function testCompositionIsFetched(): void
    {
        $compositionSpy = new CompositionSpy();

        $useCase = $this->createUseCase();
        $useCase->setCompositionRepository($compositionSpy);
        $useCase->execute();

        $this->assertTrue(
            $compositionSpy->hasFetchCompositionByIdBeenCalled()
        );
    }

    public function testChangesAreFetched(): void
    {
        $compositionSpy = new CompositionSpy();

        $useCase = $this->createUseCase();
        $useCase->setCompositionRepository($compositionSpy);
        $useCase->execute();

        $this->assertTrue(
            $compositionSpy->hasFetchChangesByCompositionBeenCalled()
        );
    }

    public function testSuccessfulResponse(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $expectedData = [
            'compositionName' => TestConstants::TEST_COMPOSITION_NAME,
            'numberOfChanges' => 1,
            'changes' => [
                [
                    'changeNumber' => 1,
                    'changeText' => '1 - 2',
                ]
            ]
        ];

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
        );
        $this->assertEquals(
            $expectedData,
            $response->getData()
        );
    }

    public function testSuccessfulResponseCalledDown(): void
    {
        $request = new ViewCompositionRequest();
        $request->setCompositionId(TestConstants::TEST_COMPOSITION_ID);
        $request->setUpChanges(false);

        $change2 = $this->createMockChange(2);
        $change2->setBellToFollow(TestConstants::TEST_CHANGE_BELL_TO_LEAD);

        $compositionSpy = new CompositionSpy();
        $compositionSpy->setFetchChangesByCompositionValue(
            [
                $this->createMockChange(1),
                $change2,
            ]
        );

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setRequest($request);
        $useCase->setCompositionRepository($compositionSpy);
        $useCase->execute();
        $response = $presenterSpy->getResponse();

        $expectedData = [
            'compositionName' => TestConstants::TEST_COMPOSITION_NAME,
            'numberOfChanges' => 2,
            'changes' => [
                [
                    'changeNumber' => 1,
                    'changeText' => '2 - 3',
                ],
                [
                    'changeNumber' => 2,
                    'changeText' => '2 - Lead',
                ]
            ]
        ];

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
        );
        $this->assertEquals(
            $expectedData,
            $response->getData()
        );
    }

}
