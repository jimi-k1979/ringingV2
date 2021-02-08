<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\ringer\RingerFuzzySearch;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\LoggedInUserDummy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\RingerDummy;
use DrlArchive\mocks\RingerSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use PHPUnit\Framework\TestCase;
use DrlArchive\traits\CreateMockRingerTrait;

class RingerFuzzySearchTest extends TestCase
{
    use CreateMockRingerTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new RingerFuzzySearch()
        );
    }

    public function testGuestUserIsAuthorised(): void
    {
        $useCase = $this->createUseCase();

        $securitySpy = new SecurityRepositorySpy();
        $userSpy = new GuestUserDummy();

        $useCase->setSecurityRepository($securitySpy);
        $useCase->setUserRepository($userSpy);
        $useCase->execute();

        $this->assertTrue(
            $securitySpy->hasIsUserAuthorisedCalled()
        );
    }

    /**
     * @return RingerFuzzySearch
     */
    public function createUseCase(): RingerFuzzySearch
    {
        $request = new RingerFuzzySearchRequest(
            [
                RingerFuzzySearchRequest::SEARCH_TERM => 'st rin',
            ]
        );

        $useCase = new RingerFuzzySearch();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setUserRepository(new LoggedInUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setRingerRepository(new RingerDummy());

        return $useCase;
    }

    public function testRingerListIsCalled(): void
    {
        $ringerSpy = new RingerSpy();

        $useCase = $this->createUseCase();
        $useCase->setRingerRepository($ringerSpy);
        $useCase->execute();

        $this->assertTrue(
            $ringerSpy->hasFuzzySearchRingerBeenCalled()
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

    public function testRespeonseWithMultipleResults(): void
    {
        $ringer = new RingerEntity();
        $ringer->setId(111);
        $ringer->setFirstName('Joest');
        $ringer->setLastName('Ritter');

        $ringerSpy = new RingerSpy();
        $ringerSpy->setFuzzySearchValue(
            [
                $ringer,
                $this->createMockRinger(),
            ]
        );

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setRingerRepository($ringerSpy);
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );

        $expectedResponse = [
            [
                'id' => 111,
                'firstName' => 'Joest',
                'lastName' => 'Ritter',
                'fullName' => 'Joest Ritter',
            ],
            [
                'id' => 4321,
                'firstName' => 'Test',
                'lastName' => 'Ringer',
                'fullName' => 'Test Ringer',
            ],
        ];

        $this->assertEquals(
            $expectedResponse,
            $response->getData()
        );
    }

    public function testEmptyResponse(): void
    {
        $ringerSpy = new RingerSpy();
        $ringerSpy->setFuzzySearchThrowsException();

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setRingerRepository($ringerSpy);
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
