<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\location\fetchLocationByName;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\LocationDummy;
use DrlArchive\mocks\LocationSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;

class FetchLocationByNameTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new FetchLocationByName()
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

    private function createUseCase(): FetchLocationByName
    {
        $request = new FetchLocationByNameRequest();
        $request->setName(TestConstants::TEST_LOCATION_NAME);

        $useCase = new FetchLocationByName();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setUserRepository(new GuestUserDummy());
        $useCase->setLocationRepository(new LocationDummy());

        return $useCase;
    }

    public function testDataIsFetched(): void
    {
        $locationSpy = new LocationSpy();

        $useCase = $this->createUseCase();
        $useCase->setLocationRepository($locationSpy);
        $useCase->execute();

        $this->assertTrue(
            $locationSpy->hasFetchLocationByNameBeenCalled()
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

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                'locationId' => TestConstants::TEST_LOCATION_ID,
                'name' => TestConstants::TEST_LOCATION_NAME,
                'dedication' => TestConstants::TEST_LOCATION_DEDICATION,
                'tenorWeight' => TestConstants::TEST_LOCATION_WEIGHT,
                'numberOfBells' => TestConstants::TEST_LOCATION_NUMBER_OF_BELLS,
                'deanery' => TestConstants::TEST_DEANERY_NAME,
                'region' => TestConstants::TEST_DEANERY_REGION,
            ],
            $response->getData()
        );
    }

    public function testFailureResponse(): void
    {
        $locationSpy = new LocationSpy();
        $locationSpy->setFetchLocationByNameThrowsException();

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setLocationRepository($locationSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_NOT_FOUND,
            $response->getStatus()
        );
        $this->assertEquals(
            '2102: No location with that name',
            $response->getMessage()
        );
    }

}
