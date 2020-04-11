<?php

declare(strict_types=1);

namespace core\interactors\event\readDrlEvent;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\event\readDrlEvent\ReadDrlEvent;
use DrlArchive\core\interactors\event\readDrlEvent\ReadDrlEventRequest;
use DrlArchive\core\interactors\Interactor;
use mocks\EventDummy;
use mocks\EventSpy;
use mocks\GuestUserDummy;
use mocks\LoggedInUserDummy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
use mocks\ResultDummy;
use mocks\ResultSpy;
use mocks\SecurityRepositoryDummy;
use mocks\SecurityRepositorySpy;
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
                ReadDrlEventRequest::DRL_EVENT_ID => 123,
            ]
        );

        $useCase = new ReadDrlEvent();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
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
                'id' => 1234,
                'competition' => 'Test competition',
                'location' => 'Test tower',
                'year' => '1970',
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
                'id' => 1234,
                'competition' => 'Test competition',
                'location' => 'Test tower',
                'year' => '1970',
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
        $eventSpy->setFetchEventThrowsException();

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
