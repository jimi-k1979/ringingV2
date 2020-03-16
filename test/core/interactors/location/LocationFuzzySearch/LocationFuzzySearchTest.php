<?php

declare(strict_types=1);

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interactors\location\locationFuzzySearch\LocationFuzzySearch;
use DrlArchive\core\interactors\location\locationFuzzySearch\LocationFuzzySearchRequest;
use mocks\LocationDummy;
use mocks\LocationSpy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
use PHPUnit\Framework\TestCase;
use traits\CreateMockLocationTrait;

class LocationFuzzySearchTest extends TestCase
{
    use CreateMockLocationTrait;

    public function testInstantiation(): void
    {
        $useCase = new LocationFuzzySearch();

        $this->assertInstanceOf(
            Interactor::class,
            $useCase
        );
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
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setLocationRepository(new LocationDummy());
        return $useCase;
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
                'id' => 999,
                'location' => 'Test tower',
            ],
            [
                'id' => 333,
                'location' => 'Test Location',
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
                    'id' => 999,
                    'location' => 'Test tower',
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
