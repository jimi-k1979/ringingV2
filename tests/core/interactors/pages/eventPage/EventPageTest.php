<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\eventPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\EventDummy;
use DrlArchive\mocks\EventSpy;
use DrlArchive\mocks\JudgeDummy;
use DrlArchive\mocks\JudgeSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\ResultDummy;
use DrlArchive\mocks\ResultSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;

class EventPageTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new EventPage()
        );
    }

    public function testRequestDefaults(): void
    {
        $request = new EventPageRequest();

        $this->assertEquals(
            0,
            $request->getEventId()
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

    private function createUseCase(): EventPage
    {
        $request = new EventPageRequest();
        $request->setEventId(TestConstants::TEST_EVENT_ID);

        $useCase = new EventPage();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setEventRepository(new EventDummy());
        $useCase->setResultRepository(new ResultDummy());
        $useCase->setJudgeRepository(new JudgeDummy());

        return $useCase;
    }

    public function testUserDetailsFetchedIfLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
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

    public function testResponseForNoEventId(): void
    {
        $request = new EventPageRequest();

        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->setRequest($request);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_BAD_REQUEST,
            $response->getStatus()
        );
        $this->assertEquals(
            'No event id given',
            $response->getMessage()
        );
    }

    public function testEventDetailsFetched(): void
    {
        $eventSpy = new EventSpy();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $eventSpy->hasFetchDrlEventBeenCalled()
        );
    }

    public function testResultsAreFetched(): void
    {
        $resultSpy = new ResultSpy();

        $useCase = $this->createUseCase();
        $useCase->setResultRepository($resultSpy);
        $useCase->execute();

        $this->assertTrue(
            $resultSpy->hasFetchDrlEventResultsBeenCalled()
        );
    }

    public function testJudgesAreFetch(): void
    {
        $judgeSpy = new JudgeSpy();

        $useCase = $this->createUseCase();
        $useCase->setJudgeRepository($judgeSpy);
        $useCase->execute();

        $this->assertTrue(
            $judgeSpy->hasFetchJudgesByDrlEventBeenCalled()
        );
    }

    public function testSuccessfulResponse(): void
    {
        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $response = $presenter->getResponse();

        $expectedData = [
            'eventId' => TestConstants::TEST_EVENT_ID,
            'eventYear' => TestConstants::TEST_EVENT_YEAR,
            'eventLocation' => TestConstants::TEST_LOCATION_NAME,
            'isUnusualLocation' => TestConstants::TEST_EVENT_UNUSUAL_TOWER,
            'competitionName' => TestConstants::TEST_DRL_COMPETITION_NAME,
            'results' => [
                [
                    'position' => 1,
                    'faults' => 10.25,
                    'team' => 'Team 1',
                    'pealNumber' => 4,
                ],
                [
                    'position' => 2,
                    'faults' => 20.5,
                    'team' => 'Team 2',
                    'pealNumber' => 3,
                ],
                [
                    'position' => 3,
                    'faults' => 30.75,
                    'team' => 'Team 3',
                    'pealNumber' => 2,
                ],
                [
                    'position' => 4,
                    'faults' => 41.0,
                    'team' => 'Team 4',
                    'pealNumber' => 1,
                ],
            ],
            'judges' => [
                [
                    'id' => 4321,
                    'name' => 'Test Judge',
                ],
            ],
        ];
        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEquals(
            $expectedData,
            $response->getData()
        );
    }

}
