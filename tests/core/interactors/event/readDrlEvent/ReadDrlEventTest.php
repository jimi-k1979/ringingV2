<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\readDrlEvent;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\EventDummy;
use DrlArchive\mocks\EventSpy;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\LoggedInUserDummy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\ResultDummy;
use DrlArchive\mocks\ResultSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;

class ReadDrlEventTest extends TestCase
{

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new ReadDrlEvent()
        );
    }

    public function testGuestUserIsAuthorised(): void
    {
        $securitySpy = new SecurityRepositorySpy();

        $useCase = $this->createUseCase();
        $useCase->setUserRepository(new GuestUserDummy());
        $useCase->setSecurityRepository($securitySpy);
        $useCase->execute();

        $this->assertTrue(
            $securitySpy->hasIsUserAuthorisedCalled()
        );
    }

    /**
     * @return ReadDrlEvent
     */
    public function createUseCase(): ReadDrlEvent
    {
        $request = new ReadDrlEventRequest(
            [
                ReadDrlEventRequest::DRL_EVENT_ID => TestConstants::TEST_EVENT_ID,
            ]
        );

        $useCase = new ReadDrlEvent();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setUserRepository(new LoggedInUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setEventRepository(new EventDummy());
        $useCase->setResultRepository(new ResultDummy());
        return $useCase;
    }

    public function testFetchEventIsCalled(): void
    {
        $eventSpy = new EventSpy();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $eventSpy->hasFetchDrlEventBeenCalled()
        );
    }

    public function testFetchAllResultsIsCalled(): void
    {
        $resultSpy = new ResultSpy();

        $useCase = $this->createUseCase();
        $useCase->setResultRepository($resultSpy);
        $useCase->execute();

        $this->assertTrue(
            $resultSpy->hasFetchDrlEventResultsBeenCalled()
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

    public function testSuccessfulResponse(): void
    {
        $presenterSpy = new PresenterSpy();

        $expectedResponse = [
            'event' => [
                'id' => TestConstants::TEST_EVENT_ID,
                'competition' => TestConstants::TEST_DRL_COMPETITION_NAME,
                'location' => TestConstants::TEST_LOCATION_NAME,
                'year' => TestConstants::TEST_EVENT_YEAR,
                'results' => [
                    [
                        'position' => 1,
                        'pealNumber' => 4,
                        'faults' => 10.25,
                        'team' => 'Team 1',
                        'points' => 6,
                    ],
                    [
                        'position' => 2,
                        'pealNumber' => 3,
                        'faults' => 20.5,
                        'team' => 'Team 2',
                        'points' => 4,
                    ],
                    [
                        'position' => 3,
                        'pealNumber' => 2,
                        'faults' => 30.75,
                        'team' => 'Team 3',
                        'points' => 2,
                    ],
                    [
                        'position' => 4,
                        'pealNumber' => 1,
                        'faults' => 41.0,
                        'team' => 'Team 4',
                        'points' => 0,
                    ],
                ],
            ],
        ];

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }

    public function testNoResultsResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $resultSpy = new ResultSpy();
        $resultSpy->setFetchDrlEventResultsThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setResultRepository($resultSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );

        $expectedResponse = [
            'event' => [
                'id' => TestConstants::TEST_EVENT_ID,
                'competition' => TestConstants::TEST_DRL_COMPETITION_NAME,
                'location' => TestConstants::TEST_LOCATION_NAME,
                'year' => TestConstants::TEST_EVENT_YEAR,
                'results' => [],
            ],
        ];

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }

    public function testNoEventResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_NOT_FOUND,
            $response->getStatus()
        );

        $this->assertEmpty(
            $response->getData()
        );

        $this->assertEquals(
            'Event not found',
            $response->getMessage()
        );
    }
}
