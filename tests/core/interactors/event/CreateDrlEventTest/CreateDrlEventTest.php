<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\CreateDrlEventTest;

use DrlArchive\core\classes\Response;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\interactors\event\createDrlEvent\CreateDrlEvent;
use DrlArchive\core\interactors\event\createDrlEvent\CreateDrlEventRequest;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\mocks\EventDummy;
use DrlArchive\mocks\EventSpy;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\LoggedInUserDummy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\mocks\TransactionManagerDummy;
use DrlArchive\mocks\TransactionManagerSpy;
use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;
use DrlArchive\traits\CreateMockDrlCompetitionTrait;
use DrlArchive\traits\CreateMockLocationTrait;

class CreateDrlEventTest extends TestCase
{
    use CreateMockLocationTrait;
    use CreateMockDrlCompetitionTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new CreateDrlEvent()
        );
    }

    public function testUserIsAuthorised(): void
    {
        $securitySpy = new SecurityRepositorySpy();

        $useCase = $this->createNewUseCase();
        $useCase->setSecurityRepository($securitySpy);
        $useCase->execute();

        $this->assertTrue(
            $securitySpy->hasIsUserAuthorisedCalled()
        );
    }

    private function createNewUseCase(): CreateDrlEvent
    {
        $request = new CreateDrlEventRequest(
            [
                CreateDrlEventRequest::LOCATION_ID => TestConstants::TEST_LOCATION_ID,
                CreateDrlEventRequest::COMPETITION_ID => TestConstants::TEST_DRL_COMPETITION_ID,
                CreateDrlEventRequest::YEAR => TestConstants::TEST_EVENT_YEAR,
            ]
        );

        $useCase = new CreateDrlEvent();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setEventRepository(new EventDummy());
        $useCase->setTransactionRepository(new TransactionManagerDummy());
        $useCase->setUserRepository(new LoggedInUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());

        return $useCase;
    }

    public function testGuestUserIsUnauthorised(): void
    {
        $userSpy = new GuestUserDummy();
        $securitySpy = new SecurityRepositorySpy();

        $this->expectException(AccessDeniedException::class);

        $useCase = $this->createNewUseCase();
        $useCase->setUserRepository($userSpy);
        $useCase->setSecurityRepository($securitySpy);
        $useCase->execute();
    }

    public function testTransactionIsStarted(): void
    {
        $transactionSpy = new TransactionManagerSpy();

        $useCase = $this->createNewUseCase();
        $useCase->setTransactionRepository($transactionSpy);
        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasStartTransactionBeenCalled()
        );
    }

    public function testInsertIsCalled(): void
    {
        $eventSpy = new EventSpy();

        $useCase = $this->createNewUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $eventSpy->hasInsertEventBeenCalled()
        );
    }

    public function testTransactionIsRolledBack(): void
    {
        $transactionSpy = new TransactionManagerSpy();
        $eventSpy = new EventSpy();
        $eventSpy->setInsertDrlEventThrowsException();

        $useCase = $this->createNewUseCase();
        $useCase->setTransactionRepository($transactionSpy);
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasRollbackTransactionBeenCalled()
        );
    }

    public function testTransactionIsCommitted(): void
    {
        $transactionSpy = new TransactionManagerSpy();

        $useCase = $this->createNewUseCase();
        $useCase->setTransactionRepository($transactionSpy);
        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasCommitTransactionBeenCalled()
        );
    }

    public function testSendIsCalled(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createNewUseCase();
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
        $eventSpy->setInsertDrlEventIdValue(TestConstants::TEST_EVENT_ID);

        $useCase = $this->createNewUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );

        $expectedResponse = [
            'drlEventId' => TestConstants::TEST_EVENT_ID,
            'locationId' => TestConstants::TEST_LOCATION_ID,
            'competitionId' => TestConstants::TEST_DRL_COMPETITION_ID,
            'year' => TestConstants::TEST_EVENT_YEAR,
        ];

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }

    public function testFailingResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $eventSpy = new EventSpy();
        $eventSpy->setInsertDrlEventThrowsException();

        $useCase = $this->createNewUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_NOT_CREATED,
            $response->getStatus()
        );

        $expectedResponse = [
            'message' => "Can't insert event",
            'code' => EventRepositoryInterface::NO_ROWS_CREATED_EXCEPTION,
        ];

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }
}
