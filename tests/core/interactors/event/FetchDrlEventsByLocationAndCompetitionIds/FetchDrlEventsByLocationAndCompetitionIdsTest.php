<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchDrlEventsByLocationAndCompetitionIds;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\mocks\EventDummy;
use DrlArchive\mocks\EventSpy;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;
use DrlArchive\traits\CreateMockDrlEventTrait;

class FetchDrlEventsByLocationAndCompetitionIdsTest extends TestCase
{
    use CreateMockDrlEventTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new FetchDrlEventsByLocationAndCompetitionIds()
        );
    }

    public function testCheckUserAuthorisation(): void
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
     * @return FetchDrlEventsByLocationAndCompetitionIds
     */
    private function createUseCase(): FetchDrlEventsByLocationAndCompetitionIds
    {
        $request = new FetchDrlEventsByLocationAndCompetitionIdsRequest(
            [
                FetchDrlEventsByLocationAndCompetitionIdsRequest::COMPETITION_ID =>
                    TestConstants::TEST_DRL_COMPETITION_ID,
                FetchDrlEventsByLocationAndCompetitionIdsRequest::LOCATION_ID =>
                    TestConstants::TEST_LOCATION_ID,
            ]
        );

        $useCase = new FetchDrlEventsByLocationAndCompetitionIds();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setUserRepository(new GuestUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setEventRepository(new EventDummy());
        return $useCase;
    }

    public function testDataIsFetched(): void
    {
        $eventSpy = new EventSpy();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $eventSpy->hasFetchDrlEventsByCompetitionAndLocationIdsBeenCalled()
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
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventsByCompetitionAndLocationIdsValue(
            [$this->createMockDrlEvent()]
        );

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $expectedResponse = [
            [
                'id' => TestConstants::TEST_EVENT_ID,
                'text' => TestConstants::TEST_EVENT_YEAR,
            ],
        ];

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
            'Incorrect response status'
        );
        $this->assertEquals(
            $expectedResponse,
            $response->getData(),
            'Incorrect response data'
        );
    }

    public function testFailingResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventsByCompetitionAndLocationIdsThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_NOT_FOUND,
            $response->getStatus(),
            'Incorrect response status'
        );
        $this->assertEquals(
            'No events found',
            $response->getMessage(),
            'Incorrect response message'
        );
        $this->assertEquals(
            [
                'code' => EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION,
            ],
            $response->getData(),
            'Incorrect response data'
        );
    }
}
