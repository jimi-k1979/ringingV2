<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\location\createLocation;

use DrlArchive\core\classes\Response;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\DeaneryDummy;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\LocationDummy;
use DrlArchive\mocks\LocationSpy;
use DrlArchive\mocks\LoggedInUserDummy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\mocks\TransactionManagerDummy;
use DrlArchive\mocks\TransactionManagerSpy;
use DrlArchive\TestConstants;
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
        $request = new CreateLocationRequest(
            [
                CreateLocationRequest::LOCATION_NAME => TestConstants::TEST_LOCATION_NAME,
                CreateLocationRequest::DEANERY => TestConstants::TEST_DEANERY_ID,
                CreateLocationRequest::DEDICATION => TestConstants::TEST_LOCATION_DEDICATION,
                CreateLocationRequest::TENOR_WEIGHT => TestConstants::TEST_LOCATION_WEIGHT,
            ]
        );
        $useCase = new CreateLocation();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setDeaneryRepository(new DeaneryDummy());
        $useCase->setLocationRepository(new LocationDummy());
        $useCase->setTransactionManager(new TransactionManagerDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setUserRepository(new LoggedInUserDummy());

        return $useCase;
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

    public function testGuestUserIsNotAuthorised(): void
    {
        $securitySpy = new SecurityRepositorySpy();
        $authenticationSpy = new AuthenticationManagerSpy();

        $this->expectException(
            AccessDeniedException::class
        );

        $useCase = $this->createUseCase();
        $useCase->setSecurityRepository($securitySpy);
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();
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
        $locationSpy->setInsertThrowsException();

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
            'id' => TestConstants::TEST_LOCATION_ID,
            'location' => TestConstants::TEST_LOCATION_NAME,
            'deanery' => TestConstants::TEST_DEANERY_NAME,
            'dedication' => TestConstants::TEST_LOCATION_DEDICATION,
            'tenorWeight' => TestConstants::TEST_LOCATION_WEIGHT,
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
        $locationSpy->setInsertThrowsException();

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
