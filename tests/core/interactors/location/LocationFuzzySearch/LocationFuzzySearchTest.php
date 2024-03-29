<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\location\LocationFuzzySearch;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\LocationDummy;
use DrlArchive\mocks\LocationSpy;
use DrlArchive\mocks\LoggedInUserDummy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;
use DrlArchive\traits\CreateMockLocationTrait;

class LocationFuzzySearchTest extends TestCase
{
    use CreateMockLocationTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new LocationFuzzySearch()
        );
    }

    public function testGuestUserIsAuthorised(): void
    {
        $userSpy = new GuestUserDummy();
        $securitySpy = new SecurityRepositorySpy();

        $useCase = $this->createUseCase();
        $useCase->setSecurityRepository($securitySpy);
        $useCase->setUserRepository($userSpy);
        $useCase->execute();

        $this->assertTrue(
            $securitySpy->hasIsUserAuthorisedCalled()
        );
    }

    /**
     * @return LocationFuzzySearch
     */
    public function createUseCase(): LocationFuzzySearch
    {
        $request = new LocationFuzzySearchRequest(
            [
                LocationFuzzySearchRequest::SEARCH_TERM => 'test',
            ]
        );

        $useCase = new LocationFuzzySearch();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setLocationRepository(new LocationDummy());
        $useCase->setUserRepository(new LoggedInUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        return $useCase;
    }

    public function testGetLocationListIsCalled(): void
    {
        $useCase = $this->createUseCase();

        $locationSpy = new LocationSpy();

        $useCase->setLocationRepository($locationSpy);
        $useCase->execute();

        $this->assertTrue(
            $locationSpy->hasFuzzySearchLocationBeenCalled()
        );
    }

    public function testSendIsCalled()
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $this->assertTrue(
            $presenterSpy->hasSendBeenCalled()
        );
    }

    public function testResponseWithMultipleResults(): void
    {
        $presenterSpy = new PresenterSpy();

        $location = new LocationEntity();
        $location->setLocation('Test Location');
        $location->setId(333);

        $locationSpy = new LocationSpy();
        $locationSpy->setFuzzySearchValue(
            [
                $this->createMockLocation(),
                $location,
            ]
        );

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setLocationRepository($locationSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $expectedResponse = [
            [
                'id' => TestConstants::TEST_LOCATION_ID,
                'name' => TestConstants::TEST_LOCATION_NAME,
            ],
            [
                'id' => 333,
                'name' => 'Test Location',
            ]
        ];

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
            'Incorrect response status'
        );

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }

    public function testSingleValueResponse(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            [
                [
                    'id' => TestConstants::TEST_LOCATION_ID,
                    'name' => TestConstants::TEST_LOCATION_NAME,
                ],
            ],
            $response->getData()
        );
    }

    public function testEmptyResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $locationSpy = new LocationSpy();
        $locationSpy->setFuzzySearchValue([]);

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setLocationRepository($locationSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            [],
            $response->getData()
        );
    }
}
