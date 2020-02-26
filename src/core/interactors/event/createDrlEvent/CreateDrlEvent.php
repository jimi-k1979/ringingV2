<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\createDrlEvent;


use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\DrlEventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use DrlArchive\core\interfaces\repositories\TransactionManagerInterface;
use Exception;

class CreateDrlEvent extends Interactor
{

    /**
     * @var DrlEventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var TransactionManagerInterface
     */
    private $transactionManager;
    /**
     * @var DrlEventEntity
     */
    private $eventEntity;

    /**
     * @param DrlEventRepositoryInterface $eventRepository
     */
    public function setEventRepository(
        DrlEventRepositoryInterface $eventRepository
    ): void {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param TransactionManagerInterface $transactionManager
     */
    public function setTransactionRepository(
        TransactionManagerInterface $transactionManager
    ): void {
        $this->transactionManager = $transactionManager;
    }

    public function execute(): void
    {
        try {
            $this->transactionManager->startTransaction();
            $this->createEntity();
            $this->insertNewEvent();
            $this->transactionManager->commitTransaction();
        } catch (Exception $e) {
            $this->transactionManager->rollbackTransaction();
        }
    }

    private function createEntity(): void
    {
        $competition = new DrlCompetitionEntity();
        $competition->setId($this->request->getCompetitionId());

        $location = new LocationEntity();
        $location->setId($this->request->getLocationId());

        $judges = [];
        if (null !== $this->request->getJudges()) {
            $judgeEntity = new JudgeEntity();
            foreach ($this->request->getJudges() as $judge) {
                $judgeEntity->setId($judge);
                $judges[] = clone $judgeEntity;
            }
        }

        $this->eventEntity = new DrlEventEntity();
        $this->eventEntity->setCompetition($competition);
        $this->eventEntity->setLocation($location);
        $this->eventEntity->setYear(
            $this->request->getYear()
        );
        $this->eventEntity->setJudges($judges);
    }

    private function insertNewEvent(): void
    {
        $this->eventEntity = $this->eventRepository->insertEvent(
            $this->eventEntity
        );
    }
}