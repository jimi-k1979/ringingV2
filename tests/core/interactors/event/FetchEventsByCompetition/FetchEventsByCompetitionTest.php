<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchEventsByCompetition;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\EventDummy;
use DrlArchive\mocks\EventSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;
use DrlArchive\traits\CreateMockDrlEventTrait;

class FetchEventsByCompetitionTest extends TestCase
{
    use CreateMockDrlEventTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new FetchEventsByCompetition()
        );
    }

    public function testGuestUserIsAuthorised(): void
    {
        $securitySpy = new SecurityRepositorySpy();

        $useCase = $this->createUseCase();
        $useCase->setSecurityRepository($securitySpy);
        $useCase->execute();

        $this->assertTrue(
            $securitySpy->hasIsUserAuthorisedCalled()
        );
    }

    private function createUseCase(): FetchEventsByCompetition
    {
        $request = new FetchEventsByCompetitionRequest(
            [
                FetchEventsByCompetitionRequest::COMPETITION =>
                    TestConstants::TEST_DRL_COMPETITION_NAME,
                FetchEventsByCompetitionRequest::COMPETITION_TYPE =>
                    AbstractCompetitionEntity::COMPETITION_TYPE_DRL,
            ]
        );
        $useCase = new FetchEventsByCompetition();

        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setEventRepository(new EventDummy());

        return $useCase;
    }

    public function testGetEventListIsCalled(): void
    {
        $eventSpy = new EventSpy();
        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $eventSpy->hasFetchDrlEventsByCompetitionNameBeenCalled()
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

    public function testResultResponse(): void
    {
        $event = new DrlEventEntity();
        $event->setYear('1989');
        $event->setId(555);

        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventsByCompetitionNameValue(
            [
                $this->createMockDrlEvent(),
                $event,
            ]
        );

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $expectedResponse = [
            [
                'id' => TestConstants::TEST_EVENT_ID,
                'text' => TestConstants::TEST_EVENT_YEAR,
            ],
            [
                'id' => 555,
                'text' => '1989',
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

    public function testNoResultsResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $eventSpy = new EventSpy();
        $eventSpy->setfetchDrlEventsByCompetitionNameThrowsException();

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
            'No events found for that competition id',
            $response->getMessage(),
            'Incorrect response message'
        );
    }
}
