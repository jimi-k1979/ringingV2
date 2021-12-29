<?php

namespace DrlArchive\core\interactors\pages\teamPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\TeamDummy;
use DrlArchive\mocks\TeamSpy;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockTeamTrait;
use DrlArchive\traits\CreateMockUserTrait;
use PHPUnit\Framework\TestCase;

class TeamPageTest extends TestCase
{
    use CreateMockUserTrait;
    use CreateMockTeamTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new TeamPage()
        );
    }

    public function testRequestDefaults(): void
    {
        $request = new TeamPageRequest();

        $this->assertEquals(
            0,
            $request->getTeamId(),
            'Invalid team id'
        );
        $this->assertTrue(
            $request->isShowStats(),
            'Invalid show stats'
        );
        $this->assertEquals(
            [
                'startYear' => 1925,
                'endYear' => null,
                'rangeSummary' => [
                    'firstYear' => true,
                    'mostRecentYear' => true,
                    'seasonCount' => false,
                    'eventCount' => true,
                    'eventsPerSeason' => false,
                    'rankingMean' => true,
                    'rankingMedian' => false,
                    'rankingMode' => false,
                    'rankingRange' => false,
                    'positionMean' => true,
                    'positionMedian' => false,
                    'positionMode' => false,
                    'positionRange' => false,
                    'faultTotal' => true,
                    'faultMean' => true,
                    'faultMedian' => false,
                    // 'faultMode' is not greatly informative
                    'faultRange' => false,
                    'faultDifferenceTotal' => true,
                    'faultDifferenceMean' => false,
                    'faultDifferenceMedian' => false,
                    // 'faultDifferenceMode' is not greatly informative
                    'faultDifferenceRange' => false,
                    'leaguePointTotal' => true,
                    'leaguePointMean' => true,
                    'leaguePointMedian' => false,
                    // 'leaguePointMode' is not greatly informative
                    'leaguePointRange' => false,
                    'noResultCount' => true,
                ],
                'seasonal' => [
                    'eventCount' => true,
                    'faultTotal' => true,
                    'faultMean' => true,
                    'faultMedian' => false,
                    'faultRange' => false,
                    'positionMean' => true,
                    'positionMedian' => false,
                    'positionMode' => false,
                    'positionRange' => false,
                    'noResultCount' => true,
                    'leaguePointTotal' => true,
                    'leaguePointMean' => true, // aka ranking
                    'leaguePointMedian' => false,
                    'leaguePointMode' => false,
                    'leaguePointRange' => false,
                    'faultDifference' => true,
                ],
            ],
            $request->getStatsOptions(),
            'Default stats option array wrong'
        );
        $this->assertFalse(
            $request->isShowResults(),
            'Invalid show results'
        );
    }

    public function testFindOutIfUserIsLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager(
            $authenticationSpy
        );
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasIsLoggedInBeenCalled()
        );
    }

    private function createUseCase(): TeamPage
    {
        $request = new TeamPageRequest();
        $request->setTeamId(TestConstants::TEST_TEAM_ID);

        $useCase = new TeamPage();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setTeamRepository(new TeamDummy());

        return $useCase;
    }

    public function testUserDetailsFetchedIfLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasLoggedInUserDetailsBeenCalled()
        );
    }

    public function testSendIsCalled(): void
    {
        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $this->assertTrue(
            $presenter->hasSendBeenCalled()
        );
    }

    public function testFailingResponseNoTeamId(): void
    {
        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->setRequest(new TeamPageRequest());
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_BAD_REQUEST,
            $response->getStatus(),
            'Incorrect response status'
        );
        $this->assertEquals(
            'No team id given',
            $response->getMessage(),
            'Incorrect response messages'
        );
        $this->assertEquals(
            $this->createMockSuperAdmin(),
            $response->getLoggedInUser(),
            'Incorrect response user'
        );
    }

    public function testTeamDataIsFetched(): void
    {
        $teamSpy = new TeamSpy();

        $useCase = $this->createUseCase();
        $useCase->setTeamRepository($teamSpy);
        $useCase->execute();

        $this->assertTrue(
            $teamSpy->hasFetchTeamByIdBeenCalled()
        );
    }

    public function testTeamStatisticsFetchedWhenRequired(): void
    {
        $teamSpy = new TeamSpy();

        $useCase = $this->createUseCase();
        $useCase->setTeamRepository($teamSpy);
        $useCase->execute();

        $this->assertTrue(
            $teamSpy->hasFetchTeamStatisticsBeenCalled()
        );
    }

    public function testTeamStatsNotFetchedWhenNotRequired(): void
    {
        $request = new TeamPageRequest();
        $request->setTeamId(TestConstants::TEST_TEAM_ID);
        $request->setShowStats(false);

        $teamSpy = new TeamSpy();

        $useCase = $this->createUseCase();
        $useCase->setRequest($request);
        $useCase->setTeamRepository($teamSpy);
        $useCase->execute();

        $this->assertFalse(
            $teamSpy->hasFetchTeamStatisticsBeenCalled()
        );
    }

    public function testTeamResultsFetchedWhenRequired(): void
    {
        $request = new TeamPageRequest();
        $request->setTeamId(TestConstants::TEST_TEAM_ID);
        $request->setShowResults(true);
        $teamSpy = new TeamSpy();

        $useCase = $this->createUseCase();
        $useCase->setRequest($request);
        $useCase->setTeamRepository($teamSpy);
        $useCase->execute();

        $this->assertTrue(
            $teamSpy->hasFetchTeamResultsBeenCalled()
        );
    }

    public function testResultsNotFetchedWhenNotRequired(): void
    {
        $teamSpy = new TeamSpy();

        $useCase = $this->createUseCase();
        $useCase->setTeamRepository($teamSpy);
        $useCase->execute();

        $this->assertFalse(
            $teamSpy->hasFetchTeamResultsBeenCalled()
        );
    }

    public function testSuccessfulResponse(): void
    {
        $request = new TeamPageRequest();
        $request->setTeamId(TestConstants::TEST_TEAM_ID);
        $request->setShowResults(true);

        $expectedResponseData = [
            'team' => [
                'id' => TestConstants::TEST_TEAM_ID,
                'name' => TestConstants::TEST_TEAM_NAME,
                'deanery' => TestConstants::TEST_DEANERY_NAME,
                'region' => TestConstants::TEST_DEANERY_REGION,
            ],
            'stats' => [
                'startYear' => 1925,
                'endYear' => date('Y'),
                'rangeSummary' => [
                    'firstYear' => 1925,
                    'mostRecentYear' => date('Y'),
                    'seasonCount' => ((int)date('Y')) - 1925,
                    'eventCount' => 2,
                    'eventsPerSeason' => 1,
                    'rankingMean' => 10,
                    'rankingMedian' => 10,
                    'rankingMode' => 10,
                    'rankingRange' => 0,
                    'positionMean' => 1,
                    'positionMedian' => 1,
                    'positionMode' => 1,
                    'positionRange' => 0,
                    'faultTotal' => 10,
                    'faultMean' => 5,
                    'faultMedian' => 5,
                    'faultRange' => 0,
                    'faultDifferenceTotal' => 200,
                    'faultDifferenceMean' => 100,
                    'faultDifferenceMedian' => 100,
                    'faultDifferenceRange' => 0,
                    'leaguePointTotal' => 20,
                    'leaguePointMean' => 10,
                    'leaguePointMedian' => 10,
                    'leaguePointRange' => 0,
                    'noResultCount' => 0,
                ],
                'seasonal' => [
                    '1925' => [
                        'eventCount' => 1,
                        'faultTotal' => 5,
                        'faultMean' => 5,
                        'faultMedian' => 5,
                        'faultRange' => 0,
                        'positionMean' => 1,
                        'positionMedian' => 1,
                        'positionMode' => 1,
                        'positionRange' => 0,
                        'noResultCount' => 0,
                        'leaguePointTotal' => 10,
                        'leaguePointMean' => 10,
                        'leaguePointMedian' => 10,
                        'leaguePointMode' => 10,
                        'leaguePointRange' => 0,
                        'faultDifference' => 100,
                    ],
                    date('Y') => [
                        'eventCount' => 1,
                        'faultTotal' => 5,
                        'faultMean' => 5,
                        'faultMedian' => 5,
                        'faultRange' => 0,
                        'positionMean' => 1,
                        'positionMedian' => 1,
                        'positionMode' => 1,
                        'positionRange' => 0,
                        'noResultCount' => 0,
                        'leaguePointTotal' => 10,
                        'leaguePointMean' => 10,
                        'leaguePointMedian' => 10,
                        'leaguePointMode' => 10,
                        'leaguePointRange' => 0,
                        'faultDifference' => 100,
                    ],
                ],
            ],
            'results' => [
                [
                    'eventId' => 1,
                    'season' => '1925',
                    'eventName' => TestConstants::TEST_DRL_COMPETITION_NAME,
                    'isUnusualTower' => TestConstants::TEST_EVENT_UNUSUAL_TOWER,
                    'eventLocation' => TestConstants::TEST_LOCATION_NAME,
                    'totalTeams' => 20,
                    'position' => TestConstants::TEST_RESULT_POSITION,
                    'pealNumber' => TestConstants::TEST_RESULT_PEAL_NUMBER,
                    'faults' => TestConstants::TEST_RESULT_FAULTS,
                ],
                [
                    'eventId' => 100,
                    'season' => date('Y'),
                    'eventName' => TestConstants::TEST_DRL_COMPETITION_NAME,
                    'isUnusualTower' => TestConstants::TEST_EVENT_UNUSUAL_TOWER,
                    'eventLocation' => TestConstants::TEST_LOCATION_NAME,
                    'totalTeams' => 20,
                    'position' => TestConstants::TEST_RESULT_POSITION,
                    'pealNumber' => TestConstants::TEST_RESULT_PEAL_NUMBER,
                    'faults' => TestConstants::TEST_RESULT_FAULTS,
                ],
            ],
            'statsOptions' => $request->getStatsOptions(),
        ];

        $presenterSpy = new PresenterSpy();

        $teamSpy = new TeamSpy();
        $teamSpy->setFetchTeamByIdValue(
            $this->createMockTeam()
        );
        $teamSpy->setFetchTeamStatisticsValue(
            [
                'startYear' => 1925,
                'endYear' => date('Y'),
                'rangeSummary' => [
                    'firstYear' => 1925,
                    'mostRecentYear' => date('Y'),
                    'seasonCount' => ((int)date('Y')) - 1925,
                    'eventCount' => 2,
                    'eventsPerSeason' => 1,
                    'rankingMean' => 10,
                    'rankingMedian' => 10,
                    'rankingMode' => 10,
                    'rankingRange' => 0,
                    'positionMean' => 1,
                    'positionMedian' => 1,
                    'positionMode' => 1,
                    'positionRange' => 0,
                    'faultTotal' => 10,
                    'faultMean' => 5,
                    'faultMedian' => 5,
                    'faultRange' => 0,
                    'faultDifferenceTotal' => 200,
                    'faultDifferenceMean' => 100,
                    'faultDifferenceMedian' => 100,
                    'faultDifferenceRange' => 0,
                    'leaguePointTotal' => 20,
                    'leaguePointMean' => 10,
                    'leaguePointMedian' => 10,
                    'leaguePointRange' => 0,
                    'noResultCount' => 0,
                ],
                'seasonal' => [
                    '1925' => [
                        'eventCount' => 1,
                        'faultTotal' => 5,
                        'faultMean' => 5,
                        'faultMedian' => 5,
                        'faultRange' => 0,
                        'positionMean' => 1,
                        'positionMedian' => 1,
                        'positionMode' => 1,
                        'positionRange' => 0,
                        'noResultCount' => 0,
                        'leaguePointTotal' => 10,
                        'leaguePointMean' => 10,
                        'leaguePointMedian' => 10,
                        'leaguePointMode' => 10,
                        'leaguePointRange' => 0,
                        'faultDifference' => 100,
                    ],
                    date('Y') => [
                        'eventCount' => 1,
                        'faultTotal' => 5,
                        'faultMean' => 5,
                        'faultMedian' => 5,
                        'faultRange' => 0,
                        'positionMean' => 1,
                        'positionMedian' => 1,
                        'positionMode' => 1,
                        'positionRange' => 0,
                        'noResultCount' => 0,
                        'leaguePointTotal' => 10,
                        'leaguePointMean' => 10,
                        'leaguePointMedian' => 10,
                        'leaguePointMode' => 10,
                        'leaguePointRange' => 0,
                        'faultDifference' => 100,
                    ],
                ],
            ]
        );
        $teamSpy->setFetchTeamResultsValue(
            [
                [
                    'eventId' => 1,
                    'season' => '1925',
                    'eventName' => TestConstants::TEST_DRL_COMPETITION_NAME,
                    'isUnusualTower' => TestConstants::TEST_EVENT_UNUSUAL_TOWER,
                    'eventLocation' => TestConstants::TEST_LOCATION_NAME,
                    'totalTeams' => 20,
                    'position' => TestConstants::TEST_RESULT_POSITION,
                    'pealNumber' => TestConstants::TEST_RESULT_PEAL_NUMBER,
                    'faults' => TestConstants::TEST_RESULT_FAULTS,
                ],
                [
                    'eventId' => 100,
                    'season' => date('Y'),
                    'eventName' => TestConstants::TEST_DRL_COMPETITION_NAME,
                    'isUnusualTower' => TestConstants::TEST_EVENT_UNUSUAL_TOWER,
                    'eventLocation' => TestConstants::TEST_LOCATION_NAME,
                    'totalTeams' => 20,
                    'position' => TestConstants::TEST_RESULT_POSITION,
                    'pealNumber' => TestConstants::TEST_RESULT_PEAL_NUMBER,
                    'faults' => TestConstants::TEST_RESULT_FAULTS,
                ],
            ]
        );

        $useCase = $this->createUseCase();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenterSpy);
        $useCase->setTeamRepository($teamSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
            'Unexpected status'
        );
        $this->assertEquals(
            $expectedResponseData,
            $response->getData(),
            'Unexpected data'
        );
    }


    public function testLoggedInResponse(): void
    {
        $presenter = new PresenterSpy();
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setLoggedInUserDetailsValue(
            $this->createMockSuperAdmin()
        );

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertEquals(
            $this->createMockSuperAdmin(),
            $presenter->getResponse()->getLoggedInUser()
        );
    }

    public function testLoggedOutResponse(): void
    {
        $presenter = new PresenterSpy();
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setIsLoggedInToFalse();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertNull(
            $presenter->getResponse()->getLoggedInUser()->getId()
        );
    }

    public function testResponseTeamOnly(): void
    {
        $request = new TeamPageRequest();
        $request->setTeamId(TestConstants::TEST_TEAM_ID);
        $request->setShowStats(false);

        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                'team' => [
                    'id' => TestConstants::TEST_TEAM_ID,
                    'name' => TestConstants::TEST_TEAM_NAME,
                    'deanery' => TestConstants::TEST_DEANERY_NAME,
                    'region' => TestConstants::TEST_DEANERY_REGION,
                ],
                'stats' => [],
                'results' => [],
                'statsOptions' => $request->getStatsOptions()
            ],
            $response->getData()
        );
    }

}
