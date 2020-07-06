<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchEventsByCompetition;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\interactors\event\FetchEventsByCompetition\FetchEventsByCompetition;
use DrlArchive\core\interactors\event\FetchEventsByCompetition\FetchEventsByCompetitionRequest;
use DrlArchive\core\interactors\Interactor;
use mocks\EventDummy;
use mocks\EventSpy;
use mocks\GuestUserDummy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
use mocks\SecurityRepositoryDummy;
use mocks\SecurityRepositorySpy;
use PHPUnit\Framework\TestCase;
use traits\CreateMockDrlEventTrait;

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
                FetchEventsByCompetitionRequest::COMPETITION_ID => 111,
                FetchEventsByCompetitionRequest::COMPETITION_TYPE =>
                    AbstractCompetitionEntity::COMPETITION_TYPE_DRL,
            ]
        );
        $useCase = new FetchEventsByCompetition();

        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setUserRepository(new GuestUserDummy());
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
            $eventSpy->hasFetchDrlEventsByCompetitionIdBeenCalled()
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
        $eventSpy->setFetchDrlEventsByCompetitionIdValue(
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
                'id' => 1234,
                'text' => '1970',
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
        $eventSpy->setThrowException();

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
