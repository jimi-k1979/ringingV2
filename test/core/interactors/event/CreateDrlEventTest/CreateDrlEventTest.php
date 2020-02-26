<?php

declare(strict_types=1);

use DrlArchive\core\interactors\event\createDrlEvent\CreateDrlEvent;
use DrlArchive\core\interactors\event\createDrlEvent\CreateDrlEventRequest;
use DrlArchive\core\interactors\Interactor;
use mocks\DrlEventDummy;
use mocks\DrlEventSpy;
use mocks\LocationDummy;
use mocks\PreseenterDummy;
use mocks\TransactionManagerDummy;
use mocks\TransactionManagerSpy;
use PHPUnit\Framework\TestCase;

class CreateDrlEventTest extends TestCase
{
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
                CreateDrlEventRequest::LOCATION_ID => 123,
                CreateDrlEventRequest::COMPETITION_ID => 123,
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
}
