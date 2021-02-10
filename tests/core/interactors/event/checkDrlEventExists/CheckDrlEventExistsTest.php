<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\checkDrlEventExists;

use DrlArchive\core\classes\Response;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\mocks\CompetitionDummy;
use DrlArchive\mocks\CompetitionSpy;
use DrlArchive\mocks\EventDummy;
use DrlArchive\mocks\EventSpy;
use DrlArchive\mocks\LoggedInUserDummy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockDrlEventTrait;
use PHPUnit\Framework\TestCase;

class CheckDrlEventExistsTest extends TestCase
{
    use CreateMockDrlEventTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new CheckDrlEventExists()
        );
    }

    /**
     * ignore until login process sorted
     * @throws AccessDeniedException
     */
    /*   public function testUserIsAuthorised(): void
       {
           $securitySpy = new SecurityRepositorySpy();
           $useCase = $this->createUseCase();

           $useCase->setSecurityRepository($securitySpy);
           $useCase->execute();

           $this->assertTrue(
               $securitySpy->hasIsUserAuthorisedCalled()
           );
       }
*/
       /**
        * @return CheckDrlEventExists
        */
    private function createUseCase(): CheckDrlEventExists
    {
        $request = new CheckDrlEventExistsRequest(
            [
                CheckDrlEventExistsRequest::EVENT_YEAR => TestConstants::TEST_EVENT_YEAR,
                CheckDrlEventExistsRequest::COMPETITION_NAME => TestConstants::TEST_DRL_COMPETITION_NAME,

            ]
        );

        $useCase = new CheckDrlEventExists();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setUserRepository(new LoggedInUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setEventRepository(new EventDummy());
        $useCase->setCompetitionRepository(new CompetitionDummy());

        return $useCase;
    }

    /**
     * ignore until login process sorted
     * @throws AccessDeniedException
     */
    /*public function testGuestUserIsUnauthorised(): void
    {
        $securitySpy = new SecurityRepositorySpy();
        $guestUser = new GuestUserDummy();

        $this->expectException(AccessDeniedException::class);

        $useCase = $this->createUseCase();
        $useCase->setSecurityRepository($securitySpy);
        $useCase->setUserRepository($guestUser);
        $useCase->execute();
    }*/

    /**
     * @throws AccessDeniedException
     */
    public function testFetchEvent(): void
    {
        $eventSpy = new EventSpy();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $eventSpy->hasFetchDrlEventByYearAndCompetitionNameBeenCalled()
        );
    }

    /**
     * @throws AccessDeniedException
     */
    public function testFetchCompetitionWhenNoEventMatch(): void
    {
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventByYearAndCompetitionNameThrowsException();

        $competitionSpy = new CompetitionSpy();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $this->assertTrue(
            $competitionSpy->hasFetchDrlCompetitionByNameBeenCalled()
        );
    }

    /**
     * @throws AccessDeniedException
     */
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

    /**
     * @throws AccessDeniedException
     */
    public function testSuccessfulResponseEventFound(): void
    {
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventByYearAndCompetitionNameValue(
            $this->createMockDrlEvent()
        );
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $expectedData = [
            'eventId' => TestConstants::TEST_EVENT_ID,
            'year' => TestConstants::TEST_EVENT_YEAR,
            'competition' => TestConstants::TEST_DRL_COMPETITION_NAME,
            'location' => TestConstants::TEST_LOCATION_NAME,
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

    /**
     * @throws AccessDeniedException
     */
    public function testSuccessfulResponseCompetitionFound(): void
    {
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventByYearAndCompetitionNameThrowsException();

        $competition = $this->createMockDrlCompetition();
        $competition->setSingleTowerCompetition(true);
        $competition->setUsualLocation($this->createMockLocation());

        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setFetchDrlCompetitionByNameValue($competition);

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->setPresenter($presenterSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $request = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $request->getStatus(),
            'Incorrect response status'
        );
        $this->assertEquals(
            [
                'competitionName' => TestConstants::TEST_DRL_COMPETITION_NAME,
                'competitionId' => TestConstants::TEST_DRL_COMPETITION_ID,
                'singleTower' => true,
                'usualLocation' => TestConstants::TEST_LOCATION_NAME,
                'usualLocationId' => TestConstants::TEST_LOCATION_ID,
            ],
            $request->getData(),
            'Incorrect response data'
        );
    }

    /**
     * @throws AccessDeniedException
     */
    public function testFailingResponseNoCompetition(): void
    {
        $presenterSpy = new PresenterSpy();

        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventByYearAndCompetitionNameThrowsException();

        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setFetchDrlCompetitionByNameThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_NOT_FOUND,
            $response->getStatus(),
            'Incorrect response status'
        );
        $this->assertEquals(
            ['code' => CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION],
            $response->getData(),
            'Incorrect response data'
        );
        $this->assertEquals(
            'No competition found',
            $response->getMessage(),
            'Incorrect response message'
        );
    }
}
