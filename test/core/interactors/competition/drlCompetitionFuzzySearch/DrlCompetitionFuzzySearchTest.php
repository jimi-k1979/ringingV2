<?php

declare(strict_types=1);

namespace core\interactors\competition\drlCompetitionFuzzySearch;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\interactors\competition\drlCompetitionFuzzySearch\DrlCompetitionFuzzySearch;
use DrlArchive\core\interactors\competition\drlCompetitionFuzzySearch\DrlCompetitionFuzzySearchRequest;
use DrlArchive\core\interactors\Interactor;
use mocks\CompetitionDummy;
use mocks\CompetitionSpy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
use PHPUnit\Framework\TestCase;
use traits\CreateMockDrlCompetitionTrait;

class DrlCompetitionFuzzySearchTest extends TestCase
{
    use CreateMockDrlCompetitionTrait;

    public function testInstantiation(): void
    {
        $useCase = $this->createNewUseCase();

        $this->assertInstanceOf(
            Interactor::class,
            $useCase
        );
    }

    private function createNewUseCase(): DrlCompetitionFuzzySearch
    {
        $request = new DrlCompetitionFuzzySearchRequest(
            [
                DrlCompetitionFuzzySearchRequest::SEARCH_TERM => 'test',
            ]
        );

        $useCase = new DrlCompetitionFuzzySearch();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setCompetitionRepository(new CompetitionDummy());

        return $useCase;
    }

    public function testGetCompetitionListIsCalled(): void
    {
        $competitionSpy = new CompetitionSpy();
        $useCase = $this->createNewUseCase();
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $this->assertTrue(
            $competitionSpy->hasFuzzySearchDrlCompetitionBeenCalled()
        );
    }

    public function testSendIsCalled(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createNewUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $this->assertTrue(
            $presenterSpy->hasSendBeenCalled()
        );
    }

    public function testMultipleResultResponse(): void
    {
        $competition = new DrlCompetitionEntity();
        $competition->setName('The Test Shield');
        $competition->setId(555);

        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setFuzzySearchDrlCompetitionValue(
            [
                $competition,
                $this->createMockDrlCompetition(),
            ]
        );

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createNewUseCase();
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $expectedResponse = [
            [
                'id' => 555,
                'name' => 'The Test Shield',
            ],
            [
                'id' => 999,
                'name' => 'Test competition',
            ],
        ];

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
            'Incorrect response status'
        );
        $this->assertEquals(
            $expectedResponse,
            $response->getData(),
            'Incorrect response data'
        );
    }

    public function testSingleResultResponse(): void
    {
        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setFuzzySearchDrlCompetitionValue(
            [
                $this->createMockDrlCompetition(),
            ]
        );

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createNewUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $expectedResponse = [
            [
                'id' => 999,
                'name' => 'Test competition',
            ],
        ];
        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
            'Incorrect response status'
        );
        $this->assertEquals(
            $expectedResponse,
            $response->getData(),
            'Incorrect response data'
        );
    }

    public function testNoResultsResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setRepositoryThrowsException();

        $useCase = $this->createNewUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $this->assertEquals(
            Response::STATUS_NOT_FOUND,
            $response->getStatus(),
            'Incorrect response'
        );
        $this->assertEquals(
            'No competitions found',
            $response->getMessage()
        );
    }
}
