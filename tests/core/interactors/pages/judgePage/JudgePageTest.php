<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\judgePage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\JudgeDummy;
use DrlArchive\mocks\JudgeSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockDrlEventTrait;
use DrlArchive\traits\CreateMockJudgeTrait;
use DrlArchive\traits\CreateMockRingerTrait;
use PHPUnit\Framework\TestCase;

class JudgePageTest extends TestCase
{
    use CreateMockJudgeTrait;
    use CreateMockRingerTrait;
    use CreateMockDrlEventTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new JudgePage()
        );
    }

    public function testRequestDefaults(): void
    {
        $request = new JudgePageRequest();

        $this->assertEquals(
            0,
            $request->getJudgeId()
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

    private function createUseCase(): JudgePage
    {
        $request = new JudgePageRequest();
        $request->setJudgeId(TestConstants::TEST_JUDGE_ID);

        $useCase = new JudgePage();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setJudgeRepository(new JudgeDummy());

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

    public function testFailingResponseNoJudgeId(): void
    {
        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->setRequest(new JudgePageRequest());
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_BAD_REQUEST,
            $response->getStatus()
        );
        $this->assertEquals(
            'No judge id given',
            $response->getMessage()
        );
    }

    public function testJudgeDataIsFetched(): void
    {
        $judgeSpy = new JudgeSpy();

        $useCase = $this->createUseCase();
        $useCase->setJudgeRepository($judgeSpy);
        $useCase->execute();

        $this->assertTrue(
            $judgeSpy->hasFetchJudgeByIdBeenCalled()
        );
    }

    public function testEventDataIsFetched(): void
    {
        $judgeSpy = new JudgeSpy();

        $useCase = $this->createUseCase();
        $useCase->setJudgeRepository($judgeSpy);
        $useCase->execute();

        $this->assertTrue(
            $judgeSpy->hasFetchJudgeDrlEventListBeenCalled()
        );
    }


    public function testSuccessfulResponse(): void
    {
        $expectedResponse = [
            'judge' => [
                'id' => TestConstants::TEST_JUDGE_ID,
                'firstName' => TestConstants::TEST_JUDGE_FIRST_NAME,
                'lastName' => TestConstants::TEST_JUDGE_LAST_NAME,
                'ringerId' => TestConstants::TEST_RINGER_ID,
            ],
            'events' => [
                [
                    'eventId' => TestConstants::TEST_EVENT_ID,
                    'year' => TestConstants::TEST_EVENT_YEAR,
                    'event' => TestConstants::TEST_DRL_COMPETITION_NAME
                        . ' @ ' . TestConstants::TEST_LOCATION_NAME,
                ]
            ],
            'statistics' => [
                'numberOfEvents' => 1,
            ],
        ];

        $judge = $this->createMockJudge();
        $judge->setRinger(
            $this->createMockRinger()
        );

        $judgeSpy = new JudgeSpy();
        $judgeSpy->setFetchJudgeByIdValue($judge);
        $judgeSpy->setFetchJudgeDrlEventListValue(
            [$this->createMockDrlEvent()]
        );

        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->setJudgeRepository($judgeSpy);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
            'Unexpected response status'
        );
        $this->assertEquals(
            $expectedResponse,
            $response->getData(),
            'Unexpected response data'
        );
    }

}
