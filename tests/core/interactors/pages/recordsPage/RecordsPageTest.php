<?php

namespace DrlArchive\core\interactors\pages\recordsPage;

use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\CompetitionDummy;
use DrlArchive\mocks\EventDummy;
use DrlArchive\mocks\EventSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\RingerDummy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\TeamDummy;
use PHPUnit\Framework\TestCase;

class RecordsPageTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new RecordsPage()
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

    private function createUseCase(): RecordsPage
    {
        $useCase = new RecordsPage();
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setCompetitionRepository(new CompetitionDummy());
        $useCase->setEventRepository(new EventDummy());
        $useCase->setTeamRepository(new TeamDummy());
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
        $presenter = new PresenterSpy();

        $usesCase = $this->createUseCase();
        $usesCase->setPresenter($presenter);
        $usesCase->execute();

        $this->assertTrue(
            $presenter->hasSendBeenCalled()
        );
    }

    public function testHighestEventEntryFetched(): void
    {
        $eventSpy = new EventSpy();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $eventSpy->hasFetchDrlEventListByEntryBeenCalled()
        );
    }

    public function testHighestAndLowestTotalFaultsFetched(): void
    {
        $eventSpy = new EventSpy();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertEquals(
            2,
            $eventSpy->getFetchDrlEventListByTotalFaultsCallCount()
        );
    }

    public function testHighestAndLowestAverageFaultsFetched(): void
    {
        $eventSpy = new EventSpy();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertEquals(
            2,
            $eventSpy->getFetchDrlEventListByMeanFaultsCallCount()
        );
    }

    public function testHighestAndLowestVictoryMarginFetched(): void
    {
        $eventSpy = new EventSpy();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertEquals(
            2,
            $eventSpy->getFetchDrlEventListByVictoryMarginCallCount()
        );
    }


}
