<?php

declare(strict_types=1);

namespace core\interactors\team\TeamFuzzySearch;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interactors\team\TeamFuzzySearch\TeamFuzzySearch;
use DrlArchive\core\interactors\team\TeamFuzzySearch\TeamFuzzySearchRequest;
use mocks\GuestUserDummy;
use mocks\LoggedInUserDummy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
use mocks\SecurityRepositoryDummy;
use mocks\SecurityRepositorySpy;
use mocks\TeamDummy;
use mocks\TeamSpy;
use PHPUnit\Framework\TestCase;
use traits\CreateMockTeamTrait;

class TeamFuzzySearchTest extends TestCase
{
    use CreateMockTeamTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new TeamFuzzySearch()
        );
    }

    public function testGuestUserIsAuthorised(): void
    {
        $useCase = $this->createUseCase();

        $securitySpy = new SecurityRepositorySpy();
        $user = new GuestUserDummy();

        $useCase->setSecurityRepository($securitySpy);
        $useCase->setUserRepository($user);
        $useCase->execute();

        $this->assertTrue(
            $securitySpy->hasIsUserAuthorisedCalled()
        );
    }

    /**
     * @return TeamFuzzySearch
     */
    public function createUseCase(): TeamFuzzySearch
    {
        $request = new TeamFuzzySearchRequest(
            [
                TeamFuzzySearchRequest::SEARCH_TERM => 'test',
            ]
        );

        $useCase = new TeamFuzzySearch();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setUserRepository(new LoggedInUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setTeamRepository(new TeamDummy());
        return $useCase;
    }

    public function testTeamListIsFetched(): void
    {
        $teamSpy = new TeamSpy();

        $useCase = $this->createUseCase();
        $useCase->setTeamRepository($teamSpy);
        $useCase->execute();

        $this->assertTrue(
            $teamSpy->hasFuzzySearchTeamBeenCalled()
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

    public function testMultipleValueResponse(): void
    {
        $team = new TeamEntity();
        $team->setName('Test Team B');
        $team->setId(9999);

        $teamSpy = new TeamSpy();
        $teamSpy->setFuzzySearchValue(
            [
                $this->createMockTeam(),
                $team,
            ]
        );

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setTeamRepository($teamSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );

        $expectedResponse = [
            [
                'id' => 123,
                'teamName' => 'Test team',
            ],
            [
                'id' => 9999,
                'teamName' => 'Test Team B'
            ],
        ];

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }

    public function testSingleValueResponse(): void
    {
        $teamSpy = new TeamSpy();
        $teamSpy->setFuzzySearchValue(
            [
                $this->createMockTeam(),
            ]
        );

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setTeamRepository($teamSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $expectedResponse = [
            [
                'id' => 123,
                'teamName' => 'Test team',
            ],
        ];

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }

    public function testNoResultsResponse(): void
    {
        $teamSpy = new TeamSpy();
        $teamSpy->setFuzzySearchThrowsException();

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setTeamRepository($teamSpy);
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );

        $this->assertEmpty(
            $response->getData()
        );
    }
}