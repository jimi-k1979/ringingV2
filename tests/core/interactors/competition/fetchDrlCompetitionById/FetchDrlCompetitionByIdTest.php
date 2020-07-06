<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionById;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interactors\competition\fetchDrlCompetitionById\FetchDrlCompetitionById;
use DrlArchive\core\interactors\competition\fetchDrlCompetitionById\FetchDrlCompetitionByIdRequest;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\mocks\CompetitionDummy;
use DrlArchive\mocks\CompetitionSpy;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\LocationDummy;
use DrlArchive\mocks\LocationSpy;
use DrlArchive\mocks\PreseenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use PHPUnit\Framework\TestCase;
use DrlArchive\traits\CreateMockDrlCompetitionTrait;
use DrlArchive\traits\CreateMockLocationTrait;

class FetchDrlCompetitionByIdTest extends TestCase
{
    use CreateMockDrlCompetitionTrait;
    use CreateMockLocationTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new FetchDrlCompetitionById()
        );
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

    /**
     * @return FetchDrlCompetitionById
     */
    private function createUseCase(): FetchDrlCompetitionById
    {
        $request = new FetchDrlCompetitionByIdRequest(
            [
                FetchDrlCompetitionByIdRequest::COMPETITION_ID => 1,
            ]
        );
        $useCase = new FetchDrlCompetitionById();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setUserRepository(new GuestUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setCompetitionRepository(new CompetitionDummy());
        $useCase->setLocationRepository(new LocationDummy());

        return $useCase;
    }

    public function testCompetitionIsFetched(): void
    {
        $competitionSpy = new CompetitionSpy();

        $useCase = $this->createUseCase();
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $this->assertTrue(
            $competitionSpy->hasSelectCompetitionBeenCalled()
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
        $competitionSpy = new CompetitionSpy();
        $competition = $this->createMockDrlCompetition();

        $competitionSpy->setSelectDrlCompetitionValue(
            $competition
        );

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
            'Incorrect response status'
        );
        $this->assertEquals(
            [
                'competition' => $competition,
            ],
            $response->getData(),
            'Incorrect response data'
        );
    }

    public function testFailingResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setRepositoryThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_NOT_FOUND,
            $response->getStatus(),
            'Incorrect response status'
        );
        $this->assertEquals(
            'Competition not found',
            $response->getMessage(),
            'Incorrect response message'
        );
        $this->assertEquals(
            [
                'code' =>
                    CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION,
            ],
            $response->getData(),
            'Incorrect response data'
        );
    }

    public function testFetchLocationWhenSingleTowerIsTrue(): void
    {
        $competition = $this->createMockDrlCompetition();
        $competition->setSingleTowerCompetition(true);
        $location = new LocationEntity();
        $location->setId(444);
        $competition->setUsualLocation($location);

        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setSelectDrlCompetitionValue($competition);

        $locationSpy = new LocationSpy();
        $locationSpy->setSelectLocationValue($location);

        $useCase = $this->createUseCase();
        $useCase->setLocationRepository($locationSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $this->assertTrue(
            $locationSpy->hasSelectLocationBeenCalled()
        );
    }

    public function testSuccessfulResponseWithLocation(): void
    {
        $competition = $this->createMockDrlCompetition();
        $competition->setSingleTowerCompetition(true);
        $compLocation = new LocationEntity();
        $compLocation->setId(555);
        $competition->setUsualLocation($compLocation);
        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setSelectDrlCompetitionValue($competition);

        $fetchedLocation = $this->createMockLocation();
        $locationSpy = new LocationSpy();
        $locationSpy->setSelectLocationValue($fetchedLocation);

        $expectedCompetition = $this->createMockDrlCompetition();
        $expectedCompetition->setSingleTowerCompetition(true);
        $expectedCompetition->setUsualLocation($fetchedLocation);

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->setLocationRepository($locationSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $this->assertEquals(
            ['competition' => $expectedCompetition],
            $response->getData()
        );
    }

}
