<?php

declare(strict_types=1);

namespace test\core\interactors\competition\fetchDrlCompetitionByLocation;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\competition\fetchDrlCompetitionByLocation\FetchDrlCompetitionByLocation;
use DrlArchive\core\interactors\competition\fetchDrlCompetitionByLocation\FetchDrlCompetitionByLocationRequest;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use test\mocks\CompetitionDummy;
use test\mocks\CompetitionSpy;
use test\mocks\GuestUserDummy;
use test\mocks\PreseenterDummy;
use test\mocks\PresenterSpy;
use test\mocks\SecurityRepositoryDummy;
use test\mocks\SecurityRepositorySpy;
use PHPUnit\Framework\TestCase;
use test\traits\CreateMockDrlCompetitionTrait;

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
                FetchDrlCompetitionByLocationRequest::LOCATION_ID => 1,
            ]
        );

        $useCase = new FetchDrlCompetitionByLocation();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
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
                'id' => 999,
                'text' => 'Test competition',
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
