<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionByLocation;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\mocks\CompetitionDummy;
use DrlArchive\mocks\CompetitionSpy;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;
use DrlArchive\traits\CreateMockDrlCompetitionTrait;

class FetchDrlCompetitionByLocationTest extends TestCase
{
    use CreateMockDrlCompetitionTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new FetchDrlCompetitionByLocation()
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
     * @return FetchDrlCompetitionByLocation
     */
    private function createUseCase(): FetchDrlCompetitionByLocation
    {
        $request = new FetchDrlCompetitionByLocationRequest(
            [
                FetchDrlCompetitionByLocationRequest::LOCATION_ID => TestConstants::TEST_LOCATION_ID,
            ]
        );

        $useCase = new FetchDrlCompetitionByLocation();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setUserRepository(new GuestUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setCompetitionRepository(new CompetitionDummy());
        return $useCase;
    }

    public function testFetchData(): void
    {
        $competitionSpy = new CompetitionSpy();

        $useCase = $this->createUseCase();
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $this->assertTrue(
            $competitionSpy->hasFetchDrlCompetitionByLocationBeenCalled()
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
        $competitionSpy->setFetchDrlCompetitionByLocationValue(
            [
                $this->createMockDrlCompetition(),
            ]
        );

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $expectedData = [
            [
                'id' => TestConstants::TEST_DRL_COMPETITION_ID,
                'text' => TestConstants::TEST_DRL_COMPETITION_NAME,
            ],
        ];

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
            'Incorrect response status'
        );

        $this->assertEquals(
            $expectedData,
            $response->getData(),
            'Incorrect response data'
        );
    }

    public function testFailingResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setFetchDrlCompetitionByLocationThrowsException();

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
            'No events found',
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
}
