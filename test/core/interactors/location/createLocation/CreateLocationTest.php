<?php
declare(strict_types=1);

namespace core\interactors\location\createLocation;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interactors\location\createLocation\CreateLocation;
use DrlArchive\core\interactors\location\createLocation\CreateLocationRequest;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use mocks\DeaneryDummy;
use mocks\LocationDummy;
use mocks\LocationSpy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
use mocks\TransactionManagerDummy;
use mocks\TransactionManagerSpy;
use PHPUnit\Framework\TestCase;

class CreateLocationTest extends TestCase
{

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            $this->createUseCase()
        );
    }

    private function createUseCase(): CreateLocation
    {
        $request = new CreateLocationRequest([
            CreateLocationRequest::LOCATION_NAME => 'Test tower',
            CreateLocationRequest::DEANERY => 1,
            CreateLocationRequest::DEDICATION => 'S Test',
            CreateLocationRequest::TENOR_WEIGHT => 'test cwt',
        ]);
        $useCase = new CreateLocation();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setDeaneryRepository(new DeaneryDummy());
        $useCase->setLocationRepository(new LocationDummy());
        $useCase->setTransactionManager(new TransactionManagerDummy());

        return $useCase;
    }

    public function testTransactionIsStarted(): void
    {
        $transactionSpy = new TransactionManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setTransactionManager($transactionSpy);
        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasStartTransactionBeenCalled()
        );
    }

    public function testInsertIsCalled(): void
    {
        $locationSpy = new LocationSpy();

        $useCase = $this->createUseCase();
        $useCase->setLocationRepository($locationSpy);
        $useCase->execute();

        $this->assertTrue(
            $locationSpy->hasInsertLocationBeenCalled()
        );
    }

    public function testTransactionIsCommitted(): void
    {
        $transactionSpy = new TransactionManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setTransactionManager($transactionSpy);
        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasCommitTransactionBeenCalled()
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

    public function testTransactionRollbackOnFail(): void
    {
        $transactionSpy = new TransactionManagerSpy();
        $locationSpy = new LocationSpy();
        $locationSpy->setRepositoryThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setTransactionManager($transactionSpy);
        $useCase->setLocationRepository($locationSpy);
        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasRollbackTransactionBeenCalled()
        );
    }

    public function testSuccessfulResponse(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );

        $expectedResponse = [
            'id' => 999,
            'location' => 'Test tower',
            'deanery' => 'Test deanery',
            'dedication' => 'S Test',
            'tenorWeight' => 'test cwt',
        ];

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }

    public function testFailingResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $locationSpy = new LocationSpy();
        $locationSpy->setRepositoryThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setLocationRepository($locationSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_NOT_CREATED,
            $response->getStatus()
        );
        $this->assertEquals(
            'Location not created',
            $response->getMessage()
        );

        $expectedData = [
            'message' => 'Unable to write new location',
            'code' => LocationRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
        ];
        $this->assertEquals(
            $expectedData,
            $response->getData()
        );
    }
}
