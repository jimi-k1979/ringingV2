<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\event\createDrlEvent;


use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\DrlEventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;

class CreateDrlEvent extends Interactor
{

    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;
    /**
     * @var DrlEventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @param LocationRepositoryInterface $locationRepository
     */
    public function setLocationRepository(
        LocationRepositoryInterface $locationRepository
    ): void {
        $this->locationRepository = $locationRepository;
    }

    /**
     * @param DrlEventRepositoryInterface $eventRepository
     */
    public function setEventRepository(
        DrlEventRepositoryInterface $eventRepository
    ): void {
        $this->eventRepository = $eventRepository;
    }


    public function execute(): void
    {
        // TODO: Implement execute() method.
    }
}