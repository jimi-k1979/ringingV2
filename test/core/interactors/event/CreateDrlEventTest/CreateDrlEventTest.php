<?php
declare(strict_types=1);

use DrlArchive\core\interactors\event\createDrlEvent\CreateDrlEvent;
use DrlArchive\core\interactors\event\createDrlEvent\CreateDrlEventRequest;
use DrlArchive\core\interactors\Interactor;
use mocks\DrlEventDummy;
use mocks\LocationDummy;
use mocks\PreseenterDummy;
use PHPUnit\Framework\TestCase;

class CreateDrlEventTest extends TestCase
{
    public function testInstantiation(): void
    {
        $useCase = $this->createNewUseCase();
        $this->assertInstanceOf(
            Interactor::class,
            new CreateDrlEvent()
        );
    }

    private function createNewUseCase(): CreateDrlEvent
    {
        $request = new CreateDrlEventRequest(
            [
                CreateDrlEventRequest::LOCATION_ID => 123,
                CreateDrlEventRequest::COMPETITION_ID => 123,
                CreateDrlEventRequest::YEAR => '1900',
            ]
        );

        $useCase = new CreateDrlEvent();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setEventRepository(new DrlEventDummy());
        $useCase->setLocationRepository(new LocationDummy());

        return $useCase;
    }
}
