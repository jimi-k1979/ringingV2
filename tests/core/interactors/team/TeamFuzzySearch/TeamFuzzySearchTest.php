<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\team\TeamFuzzySearch;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\LoggedInUserDummy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\mocks\TeamDummy;
use DrlArchive\mocks\TeamSpy;
use PHPUnit\Framework\TestCase;
use DrlArchive\traits\CreateMockTeamTrait;

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
        $useCase->setPresenter(new PresenterDummy());
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
                'name' => 'Test team',
            ],
            [
                'id' => 9999,
                'name' => 'Test Team B'
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
                'name' => 'Test team',
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
