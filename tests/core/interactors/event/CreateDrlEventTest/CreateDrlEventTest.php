<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\CreateDrlEventTest;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\interactors\event\createDrlEvent\CreateDrlEvent;
use DrlArchive\core\interactors\event\createDrlEvent\CreateDrlEventRequest;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use mocks\EventDummy;
use mocks\EventSpy;
use mocks\GuestUserDummy;
use mocks\LoggedInUserDummy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
use mocks\SecurityRepositoryDummy;
use mocks\SecurityRepositorySpy;
use mocks\TransactionManagerDummy;
use mocks\TransactionManagerSpy;
use PHPUnit\Framework\TestCase;
use traits\CreateMockDrlCompetitionTrait;
use traits\CreateMockLocationTrait;

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
                CreateDrlEventRequest::LOCATION_ID => 999,
                CreateDrlEventRequest::COMPETITION_ID => 999,
                CreateDrlEventRequest::YEAR => '1900',
            ]
        );

        $useCase = new CreateDrlEvent();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
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
        $eventSpy->setThrowException();

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
        $drlEvent = new DrlEventEntity();
        $drlEvent->setId(555);
        $drlEvent->setYear('1900');
        $drlEvent->setLocation($this->CreateMockLocation());
        $drlEvent->setCompetition($this->createMockDrlCompetition());

        $presenterSpy = new PresenterSpy();
        $eventSpy = new EventSpy();
        $eventSpy->setDrlEventValue($drlEvent);

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
            'drlEventId' => 555,
            'locationId' => 999,
            'competitionId' => 999,
            'year' => '1900',
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
        $eventSpy->setThrowException();

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
            'code' => EventRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION,
        ];

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }
}
