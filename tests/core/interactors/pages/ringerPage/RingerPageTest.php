<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\ringerPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\RingerDummy;
use DrlArchive\mocks\RingerSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;

class RingerPageTest extends TestCase
{

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new RingerPage()
        );
    }

    public function testRequestDefaults(): void
    {
        $request = new RingerPageRequest();

        $this->assertEquals(
            0,
            $request->getRingerId()
        );
    }

    public function testFindOutIfUserIsLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasIsLoggedInBeenCalled()
        );
    }

    private function createUseCase(): RingerPage
    {
        $request = new RingerPageRequest();
        $request->setRingerId(1);

        $useCase = new RingerPage();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setRingerRepository(new RingerDummy());
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
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $this->assertTrue(
            $presenterSpy->hasSendBeenCalled()
        );
    }

    public function testResponseForNoRingerId(): void
    {
        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->setRequest(new RingerPageRequest());
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_BAD_REQUEST,
            $response->getStatus()
        );
        $this->assertEquals(
            'No ringer id given',
            $response->getMessage()
        );
    }

    public function testRingerDataIsFetched(): void
    {
        $ringerSpy = new RingerSpy();

        $useCase = $this->createUseCase();
        $useCase->setRingerRepository($ringerSpy);
        $useCase->execute();

        $this->assertTrue(
            $ringerSpy->hasFetchRingerByIdBeenCalled()
        );
    }

    public function testEventDataIsFetched(): void
    {
        $ringerSpy = new RingerSpy();

        $useCase = $this->createUseCase();
        $useCase->setRingerRepository($ringerSpy);
        $useCase->execute();

        $this->assertTrue(
            $ringerSpy->hasFetchRingerEventListBeenCalled()
        );
    }

    public function testStatisticsCalculated(): void
    {
        $useCase = new class extends RingerPage {
            public function getStats(): array
            {
                return $this->stats;
            }
        };
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setRequest(
            new RingerPageRequest(
                [RingerPageRequest::RINGER_ID => TestConstants::TEST_RINGER_ID]
            )
        );
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setRingerRepository(new RingerDummy());
        $useCase->execute();

        $expectedData = [
            'numberOfWins' => 1,
            'winsByBell' => [
                'treble' => 1,
                '2' => 0,
                '3' => 0,
                '4' => 0,
                '5' => 0,
                '6' => 0,
                '7' => 0,
                'tenor' => 0,
                'strapper' => 0,
            ],
            'winsByDecade' => [
                '197' => 1,
            ],
            'winsByNumberOfBells' => [
                '6' => 1,
                '8' => 0,
            ],
            'winsByCompetition' => [
                TestConstants::TEST_DRL_COMPETITION_NAME => 1,
            ]
        ];
        $this->assertSame(
            $expectedData,
            $useCase->getStats()
        );
    }

    public function testSuccessfulResponse(): void
    {
        $expectedResponse = [
            'ringer' => [
                'id' => TestConstants::TEST_RINGER_ID,
                'firstName' => TestConstants::TEST_RINGER_FIRST_NAME,
                'lastName' => TestConstants::TEST_RINGER_LAST_NAME,
                'notes' => TestConstants::TEST_RINGER_NOTES,
                'judgeId' => TestConstants::TEST_JUDGE_ID,
            ],
            'events' => [
                [
                    'id' => TestConstants::TEST_EVENT_ID,
                    'year' => TestConstants::TEST_EVENT_YEAR,
                    'event' => TestConstants::TEST_DRL_COMPETITION_NAME
                        . ' @ ' . TestConstants::TEST_LOCATION_NAME,
                    'bell' => TestConstants::TEST_WINNING_RINGER_BELL,
                ],
            ],
            'statistics' => [
                'numberOfWins' => 1,
                'winsByBell' => [
                    'treble' => 1,
                    '2' => 0,
                    '3' => 0,
                    '4' => 0,
                    '5' => 0,
                    '6' => 0,
                    '7' => 0,
                    'tenor' => 0,
                    'strapper' => 0,
                ],
                'winsByDecade' => [
                    '197' => 1,
                ],
                'winsByNumberOfBells' => [
                    '6' => 1,
                    '8' => 0,
                ],
                'winsByCompetition' => [
                    TestConstants::TEST_DRL_COMPETITION_NAME => 1,
                ]
            ]
        ];

        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $actualResponse = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $actualResponse->getStatus(),
            'Unexpected response status'
        );
        $this->assertSame(
            $expectedResponse,
            $actualResponse->getData(),
            'Unexpected response data'
        );
    }

}
