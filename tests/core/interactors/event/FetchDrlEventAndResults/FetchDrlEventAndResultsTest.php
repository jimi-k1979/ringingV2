<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchDrlEventAndResults;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\event\FetchDrlEventAndResults\FetchDrlEventAndResults;
use DrlArchive\core\interactors\event\FetchDrlEventAndResults\FetchDrlEventAndResultsRequest;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use DrlArchive\mocks\EventDummy;
use DrlArchive\mocks\EventSpy;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\JudgeDummy;
use DrlArchive\mocks\JudgeSpy;
use DrlArchive\mocks\LocationDummy;
use DrlArchive\mocks\LocationSpy;
use DrlArchive\mocks\PreseenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\ResultDummy;
use DrlArchive\mocks\ResultSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use PHPUnit\Framework\TestCase;

class FetchDrlEventAndResultsTest extends TestCase
{

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new FetchDrlEventAndResults()
        );
    }

    public function testUserIsAuthorised(): void
    {
        $securitySpy = new SecurityRepositorySpy();

        $useCase = $this->createUseCase();

        $useCase->setSecurityRepository($securitySpy);
        $useCase->execute();

        $this->assertTrue(
            $securitySpy->hasIsUserAuthorisedCalled()
        );
    }

    /**
     * @return FetchDrlEventAndResults
     */
    private function createUseCase(): FetchDrlEventAndResults
    {
        $request = new FetchDrlEventAndResultsRequest(
            [
                FetchDrlEventAndResultsRequest::EVENT_ID => 111,
            ]
        );
        $useCase = new FetchDrlEventAndResults();

        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setUserRepository(new GuestUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setEventRepository(new EventDummy());
        $useCase->setResultRepository(new ResultDummy());
        $useCase->setJudgeRepository(new JudgeDummy());
        $useCase->setLocationRepository(new LocationDummy());

        return $useCase;
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

    public function testLocationIsFetched(): void
    {
        $locationSpy = new LocationSpy();

        $useCase = $this->createUseCase();
        $useCase->setLocationRepository($locationSpy);
        $useCase->execute();

        $this->assertTrue(
            $locationSpy->hasSelectLocationBeenCalled()
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

    public function testJudgesAreFetched(): void
    {
        $judgeSpy = new JudgeSpy();

        $useCase = $this->createUseCase();
        $useCase->setJudgeRepository($judgeSpy);
        $useCase->execute();

        $this->assertTrue(
            $judgeSpy->hasFetchJudgesByDrlEventBeenCalled()
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

    public function testSuccessfulResponse(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $expectedResponse = [
            'event' => [
                'year' => '1970',
                'competition' => 'Test competition',
                'singleTower' => false,
                'location' => 'Test tower',
                'unusualTower' => false,
            ],
            'results' => [
                [
                    'position' => 1,
                    'peal number' => 4,
                    'team' => 'Team 1',
                    'faults' => 10.25,
                ],
                [
                    'position' => 2,
                    'peal number' => 3,
                    'team' => 'Team 2',
                    'faults' => 20.5,
                ],
                [
                    'position' => 3,
                    'peal number' => 2,
                    'team' => 'Team 3',
                    'faults' => 30.75,
                ],
                [
                    'position' => 4,
                    'peal number' => 1,
                    'team' => 'Team 4',
                    'faults' => 41.0,
                ],
            ],
            'judges' => [
                [
                    'name' => 'Test Judge',
                ],
            ],
        ];

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }

    public function testResponseNoJudges(): void
    {
        $judgeSpy = new JudgeSpy();
        $judgeSpy->setRepositoryThrowsException();
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setJudgeRepository($judgeSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $expectedResponse = [
            'event' => [
                'year' => '1970',
                'competition' => 'Test competition',
                'singleTower' => false,
                'location' => 'Test tower',
                'unusualTower' => false,
            ],
            'judges' => [],
            'results' => [
                [
                    'position' => 1,
                    'peal number' => 4,
                    'team' => 'Team 1',
                    'faults' => 10.25,
                ],
                [
                    'position' => 2,
                    'peal number' => 3,
                    'team' => 'Team 2',
                    'faults' => 20.5,
                ],
                [
                    'position' => 3,
                    'peal number' => 2,
                    'team' => 'Team 3',
                    'faults' => 30.75,
                ],
                [
                    'position' => 4,
                    'peal number' => 1,
                    'team' => 'Team 4',
                    'faults' => 41.0,
                ],
            ],
        ];

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }

    public function testFailingResponseNoResults(): void
    {
        $presenterSpy = new PresenterSpy();
        $resultSpy = new ResultSpy();
        $resultSpy->setFetchDrlEventResultsThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setResultRepository($resultSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $expectedResponse = [
            'code' => ResultRepositoryInterface::NO_ROWS_FOUND_EXCEPTION,
            'event' => [
                'year' => '1970',
                'competition' => 'Test competition',
                'singleTower' => false,
                'location' => 'Test tower',
                'unusualTower' => false,
            ],
        ];

        $this->assertEquals(
            Response::STATUS_NOT_FOUND,
            $response->getStatus(),
            'Status not correct'
        );

        $this->assertEquals(
            $expectedResponse,
            $response->getData(),
            'Data not correct'
        );

        $this->assertEquals(
            'No results found',
            $response->getMessage(),
            'Message not correct'
        );
    }

    public function testFailingResponseNoLocation(): void
    {
        $presenterSpy = new PresenterSpy();
        $locationSpy = new LocationSpy();
        $locationSpy->setSelectLocationThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setLocationRepository($locationSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $expectedResponse = [
            'code' => LocationRepositoryInterface::NO_ROWS_FOUND_EXCEPTION,
        ];

        $this->assertEquals(
            Response::STATUS_NOT_FOUND,
            $response->getStatus(),
            'Status not correct'
        );

        $this->assertEquals(
            $expectedResponse,
            $response->getData(),
            'Data not correct'
        );

        $this->assertEquals(
            'No event data',
            $response->getMessage(),
            'Message not correct'
        );
    }

    public function testFailingResponseNoEvent(): void
    {
        $presenterSpy = new PresenterSpy();
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $expectedResponse = [
            'code' => EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION,
        ];

        $this->assertEquals(
            Response::STATUS_NOT_FOUND,
            $response->getStatus(),
            'Status not correct'
        );

        $this->assertEquals(
            $expectedResponse,
            $response->getData(),
            'Data not correct'
        );

        $this->assertEquals(
            'No event data',
            $response->getMessage(),
            'Message not correct'
        );
    }

}
