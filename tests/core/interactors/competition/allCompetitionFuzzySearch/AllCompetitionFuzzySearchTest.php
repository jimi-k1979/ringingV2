<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\allCompetitionFuzzySearch;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\mocks\AuthenticationManagerDummy;
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
use DrlArchive\traits\CreateMockOtherCompetitionTrait;

class AllCompetitionFuzzySearchTest extends TestCase
{
    use CreateMockDrlCompetitionTrait;
    use CreateMockOtherCompetitionTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new AllCompetitionFuzzySearch()
        );
    }

    public function testCheckUserIsAuthorised(): void
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
     * @return AllCompetitionFuzzySearch
     */
    private function createUseCase(): AllCompetitionFuzzySearch
    {
        $request = new AllCompetitionFuzzySearchRequest(
            [
                AllCompetitionFuzzySearchRequest::SEARCH_TERM => 'Hi',
            ]
        );
        $useCase = new AllCompetitionFuzzySearch();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setCompetitionRepository(new CompetitionDummy());
        return $useCase;
    }

    public function testFetchCompetitions(): void
    {
        $competitionSpy = new CompetitionSpy();

        $useCase = $this->createUseCase();
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $this->assertTrue(
            $competitionSpy->hasFuzzySearchAllCompetitionsBeenCalled()
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

        $competitions = [
            $this->createMockOtherCompetition(),
            $this->createMockDrlCompetition(),
        ];
        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setFuzzySearchAllCompetitionsValue($competitions);

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $expectedData = [
            [
                'id' => TestConstants::TEST_OTHER_COMPETITION_ID,
                'name' => TestConstants::TEST_OTHER_COMPETITION_NAME,
            ],
            [
                'id' => TestConstants::TEST_DRL_COMPETITION_ID,
                'name' => TestConstants::TEST_DRL_COMPETITION_NAME,
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
        $competitionSpy->setFuzzySearchAllCompetitionsThrowsException();

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
            'No competitions found',
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
