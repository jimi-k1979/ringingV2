<?php

declare(strict_types=1);

namespace core\interactors\competition\fetchDrlCompetitionById;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\competition\fetchDrlCompetitionById\FetchDrlCompetitionById;
use DrlArchive\core\interactors\competition\fetchDrlCompetitionById\FetchDrlCompetitionByIdRequest;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use mocks\CompetitionDummy;
use mocks\CompetitionSpy;
use mocks\GuestUserDummy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
use mocks\SecurityRepositoryDummy;
use mocks\SecurityRepositorySpy;
use PHPUnit\Framework\TestCase;
use traits\CreateMockDrlCompetitionTrait;

class FetchDrlCompetitionByIdTest extends TestCase
{
    use CreateMockDrlCompetitionTrait;

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
        $competitionSpy->setSelectDrlCompetitionValue(
            $this->createMockDrlCompetition()
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
                'competition' => $this->createMockDrlCompetition()
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
}
