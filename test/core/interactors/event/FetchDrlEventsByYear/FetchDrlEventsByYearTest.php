<?php

declare(strict_types=1);

namespace core\interactors\event\FetchDrlEventsByYear;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\event\FetchDrlEventsByYear\FetchDrlEventsByYear;
use DrlArchive\core\interactors\event\FetchDrlEventsByYear\FetchDrlEventsByYearRequest;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use PHPUnit\Framework\TestCase;
use mocks\EventDummy;
use mocks\EventSpy;
use mocks\GuestUserDummy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
use mocks\SecurityRepositoryDummy;
use mocks\SecurityRepositorySpy;

class FetchDrlEventsByYearTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new FetchDrlEventsByYear()
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
     * @return FetchDrlEventsByYear
     */
    private function createUseCase(): FetchDrlEventsByYear
    {
        $request = new FetchDrlEventsByYearRequest(
            [
                FetchDrlEventsByYearRequest::YEAR => '1970',
            ]
        );
        $useCase = new FetchDrlEventsByYear();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setUserRepository(new GuestUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setEventRepository(new EventDummy());
        return $useCase;
    }

    public function testFetchDataCalled(): void
    {
        $eventSpy = new EventSpy();

        $useCase = $this->createUseCase();
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $this->assertTrue(
            $eventSpy->hasFetchDrlEventsByYearBeenCalled()
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

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $expectedData = [
            [
                'id' => 1234,
                'text' => 'Test competition',
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
        $eventSpy = new EventSpy();
        $eventSpy->setFetchDrlEventsByYearThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setEventRepository($eventSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_NOT_FOUND,
            $response->getStatus(),
            'Incorrect response status'
        );
        $this->assertEquals(
            "No competitions found",
            $response->getMessage(),
            'Incorrect response message'
        );
        $this->assertEquals(
            [
                'code' => EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            ],
            $response->getData(),
            'Incorrect response data'
        );
    }
}
