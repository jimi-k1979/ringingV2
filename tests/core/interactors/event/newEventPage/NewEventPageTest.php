<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\newEventPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\EventDummy;
use DrlArchive\mocks\EventSpy;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\LoggedInUserDummy;
use DrlArchive\mocks\PreseenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\ResultDummy;
use DrlArchive\mocks\ResultSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\mocks\TeamDummy;
use DrlArchive\mocks\TeamSpy;
use DrlArchive\mocks\TransactionManagerDummy;
use DrlArchive\mocks\TransactionManagerSpy;
use DrlArchive\traits\CreateMockDrlEventTrait;
use DrlArchive\traits\CreateMockTeamTrait;
use PHPUnit\Framework\TestCase;

class NewEventPageTest extends TestCase
{
    use CreateMockTeamTrait;
    use CreateMockDrlEventTrait;

    public function testRequestDefaults(): void
    {
        $request = new NewEventPageRequest();

        $this->assertIsArray(
            $request->getResults()
        );
        $this->assertEmpty(
            $request->getResults()
        );
        $this->assertNull(
            $request->getUsualLocation()
        );
    }


    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new NewEventPage()
        );
    }

    public function testUserIsAuthorised(): void
    {
        $securitySpy = new SecurityRepositorySpy();

        $useCase = $this->createFreshPageUseCase();
        $useCase->setSecurityRepository($securitySpy);
        $useCase->execute();

        $this->assertTrue(
            $securitySpy->hasIsUserAuthorisedCalled()
        );
    }

    private function createFreshPageUseCase(): NewEventPage
    {
        return $this->createUseCase();
    }

    private function createUseCase(
        ?NewEventPageRequest $request = null
    ): NewEventPage {
        $useCase = new NewEventPage();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setUserRepository(new LoggedInUserDummy());
        $useCase->setTeamRepository(new TeamDummy());
        $useCase->setResultRepository(new ResultDummy());
        $useCase->setEventRepository(new EventDummy());
        $useCase->setTransactionManager(new TransactionManagerDummy());

        return $useCase;
    }

    public function testGuestUserIsBlocked(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createFreshPageUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setUserRepository(new GuestUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositorySpy());
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_FORBIDDEN,
            $response->getStatus()
        );
        $this->assertEquals(
            '9901: Not authorised to view this page',
            $response->getMessage()
        );
    }

    public function testSendIsCalled(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createFreshPageUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $this->assertTrue(
            $presenterSpy->hasSendBeenCalled()
        );
    }

    public function testNoDataGivesEmptySuccessfulResponse(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createFreshPageUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEmpty(
            $response->getData()
        );
    }

    public function testTransactionIsStarted(): void
    {
        $transactionSpy = new TransactionManagerSpy();

        $useCase = $this->createPageWithPostDataUseCase();
        $useCase->setTransactionManager($transactionSpy);
        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasStartTransactionBeenCalled()
        );
    }

    private function createPageWithPostDataUseCase(): NewEventPage
    {
        $request = new NewEventPageRequest();
        $request->setYear('2020');
        $request->setCompetitionId(57);
        $request->setLocationId(193);
        $request->setUsualLocation(193);
        $request->addResultsRow(1, 12.5, 'Test team');
        $request->addResultsRow(2, 13, 'Test team');
        $request->addResultsRow(3, -1, 'Test team');

        return $this->createUseCase($request);
    }

    public function testTeamIdIsFetchedWithPostData(): void
    {
        $teamSpy = new TeamSpy();

        $useCase = $this->createPageWithPostDataUseCase();
        $useCase->setTeamRepository($teamSpy);
        $useCase->execute();

        $this->assertEquals(
            3,
            $teamSpy->getFetchTeamByNameCallCount()
        );
    }

    public function testTeamIdNotFetchedWithNoData(): void
    {
        $teamSpy = new TeamSpy();

        $useCase = $this->createFreshPageUseCase();
        $useCase->setTeamRepository($teamSpy);
        $useCase->execute();

        $this->assertFalse(
            $teamSpy->hasFetchTeamByNameBeenCalled()
        );
    }

    public function testPostedDataPreProcessed(): void
    {
        $request = new NewEventPageRequest();
        $request->setYear('2020');
        $request->setCompetitionId(57);
        $request->setLocationId(193);
        $request->setUsualLocation(193);
        $request->addResultsRow(1, 12.5, 'Test team');
        $request->addResultsRow(2, 13, 'Test team');
        $request->addResultsRow(3, 14.33, 'Test team');

        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventByYearAndCompetitionIdThrowsException();

        $useCase = new class extends NewEventPage {
            public function getResults(): array
            {
                return $this->results;
            }

            public function getEvent(): DrlEventEntity
            {
                return $this->event;
            }
        };

        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setUserRepository(new LoggedInUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositorySpy());
        $useCase->setTeamRepository(new TeamDummy());
        $useCase->setResultRepository(new ResultDummy());
        $useCase->setTransactionManager(new TransactionManagerDummy());
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $event = $this->postedEvent();

        $result1 = new DrlResultEntity();
        $result1->setPosition(1);
        $result1->setFaults(12.5);
        $result1->setTeam($this->createMockTeam());
        $result1->setEvent($event);
        $result1->setPoints(4);

        $result2 = clone $result1;
        $result2->setPosition(2);
        $result2->setFaults(13);
        $result2->setPoints(2);

        $result3 = clone $result1;
        $result3->setPosition(3);
        $result3->setFaults(14.33);
        $result3->setPoints(0);

        $expectedData = [
            $result1,
            $result2,
            $result3,
        ];

        $this->assertEquals(
            $event,
            $useCase->getEvent()
        );
        $this->assertEquals(
            $expectedData,
            $useCase->getResults()
        );
    }

    private function postedEvent(): DrlEventEntity
    {
        $event = new DrlEventEntity();
        $event->setYear('2020');
        $event->setUnusualTower(false);
        $event->setCompetition(new DrlCompetitionEntity());
        $event->getCompetition()->setId(57);
        $event->setLocation(new LocationEntity());
        $event->getLocation()->setId(193);

        return $event;
    }

    public function testNewEventChecked(): void
    {
        $eventSpy = new EventSpy();

        $useCase = $this->createPageWithPostDataUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $eventSpy->hasFetchDrlEventByYearAndCompetitionIdBeenCalled()
        );
    }

    public function testEventIsInserted(): void
    {
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventByYearAndCompetitionIdThrowsException();

        $useCase = $this->createPageWithPostDataUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $eventSpy->hasInsertEventBeenCalled()
        );
    }

    public function testResultsAreInserted(): void
    {
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventByYearAndCompetitionIdThrowsException();

        $resultSpy = new ResultSpy();

        $useCase = $this->createPageWithPostDataUseCase();
        $useCase->setResultRepository($resultSpy);
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertEquals(
            3,
            $resultSpy->getInsertDrlResultCallCount()
        );
    }

    public function testTransactionIsCommitted(): void
    {
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventByYearAndCompetitionIdThrowsException();

        $transactionSpy = new TransactionManagerSpy();

        $useCase = $this->createPageWithPostDataUseCase();
        $useCase->setTransactionManager($transactionSpy);
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasCommitTransactionBeenCalled()
        );
    }

    public function testTransactionRollbackOnException(): void
    {
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventByYearAndCompetitionIdThrowsException();
        $eventSpy->setInsertDrlEventThrowsException();

        $transactionSpy = new TransactionManagerSpy();

        $useCase = $this->createPageWithPostDataUseCase();
        $useCase->setTransactionManager($transactionSpy);
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasRollbackTransactionBeenCalled()
        );
    }

    public function testNewEventAddedSuccessfulResponse(): void
    {
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventByYearAndCompetitionIdThrowsException();
        $eventSpy->setInsertDrlEventIdValue(123);

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createPageWithPostDataUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                'eventId' => 123,
            ],
            $response->getData()
        );
    }

    public function testPointsAreAddedToResults(): void
    {
        $request = new NewEventPageRequest();
        $request->setYear('2020');
        $request->setLocationId(193);
        $request->setCompetitionId(57);
        $request->addResultsRow(1, 2, 'Test team', 5);
        $request->addResultsRow(2, 4, 'Test team', 3);
        $request->addResultsRow(3, 4, 'Test team', 1);
        $request->addResultsRow(4, 5, 'Test team', 2);
        $request->addResultsRow(5, -1, 'Test team', 4);
        $request->addResultsRow(6, -1, 'Test team', 6);

        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventByYearAndCompetitionIdThrowsException();

        $useCase = new class extends NewEventPage {
            public function getResults(): array
            {
                return $this->results;
            }
        };

        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setUserRepository(new LoggedInUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositorySpy());
        $useCase->setTeamRepository(new TeamDummy());
        $useCase->setResultRepository(new ResultDummy());
        $useCase->setTransactionManager(new TransactionManagerDummy());
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $result1 = new DrlResultEntity();
        $result1->setTeam($this->createMockTeam());
        $result1->setEvent($this->postedEvent());
        $result1->setPosition(1);
        $result1->setFaults(2);
        $result1->setPealNumber(5);
        $result1->setPoints(10);

        $result2 = clone $result1;
        $result2->setPosition(2);
        $result2->setFaults(4);
        $result2->setPealNumber(3);
        $result2->setPoints(7);

        $result3 = clone $result2;
        $result3->setPosition(3);
        $result3->setPealNumber(1);

        $result4 = clone $result1;
        $result4->setPosition(4);
        $result4->setFaults(5);
        $result4->setPealNumber(2);
        $result4->setPoints(4);

        $result5 = clone $result1;
        $result5->setPosition(6);
        $result5->setFaults(300);
        $result5->setPealNumber(4);
        $result5->setPoints(0);

        $result6 = clone $result5;
        $result6->setPealNumber(6);

        $expectedData = [
            $result1,
            $result2,
            $result3,
            $result4,
            $result5,
            $result6,
        ];

        $this->assertEquals(
            $expectedData,
            $useCase->getResults()
        );
    }

}
