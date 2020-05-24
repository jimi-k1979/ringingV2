<?php

declare(strict_types=1);

namespace test\core\interactors\event\FetchDrlEventsByYear;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\event\FetchDrlEventsByYear\FetchDrlEventsByYear;
use DrlArchive\core\interactors\event\FetchDrlEventsByYear\FetchDrlEventsByYearRequest;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use PHPUnit\Framework\TestCase;
use test\mocks\EventDummy;
use test\mocks\EventSpy;
use test\mocks\GuestUserDummy;
use test\mocks\PreseenterDummy;
use test\mocks\PresenterSpy;
use test\mocks\SecurityRepositoryDummy;
use test\mocks\SecurityRepositorySpy;

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
                'name' => 'Test competition',
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
