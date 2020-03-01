<?php

declare(strict_types=1);

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\interactors\event\createDrlEvent\CreateDrlEvent;
use DrlArchive\core\interactors\event\createDrlEvent\CreateDrlEventRequest;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\DrlEventRepositoryInterface;
use mocks\DrlEventDummy;
use mocks\DrlEventSpy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
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
        $useCase = $this->createNewUseCase();
        $this->assertInstanceOf(
            Interactor::class,
            new CreateDrlEvent()
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
        $useCase->setEventRepository(new DrlEventDummy());
        $useCase->setTransactionRepository(new TransactionManagerDummy());

        return $useCase;
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
        $eventSpy = new DrlEventSpy();

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
        $eventSpy = new DrlEventSpy();
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
        $eventSpy = new DrlEventSpy();
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
        $eventSpy = new DrlEventSpy();
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
            'code' => DrlEventRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION,
        ];

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }
}
