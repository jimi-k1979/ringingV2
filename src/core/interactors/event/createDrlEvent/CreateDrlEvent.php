<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\createDrlEvent;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\TransactionManagerInterface;
use Exception;

class CreateDrlEvent extends Interactor
{

    /**
     * @var EventRepositoryInterface
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
     * @param EventRepositoryInterface $eventRepository
     */
    public function setEventRepository(
        EventRepositoryInterface $eventRepository
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
            $this->createResponse();
            $this->transactionManager->commitTransaction();
        } catch (Exception $e) {
            $this->transactionManager->rollbackTransaction();
            $this->createFailingResponse($e);
        }

        $this->sendResponse();
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

    private function createResponse(): void
    {
        $this->response = new CreateDrlEventResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_MESSAGE => 'Succesfully created event',
                Response::RESPONSE_DATA => [
                    'drlEventId' => $this->eventEntity->getId(),
                    'locationId' => $this->eventEntity->getLocation()->getId(),
                    'competitionId' => $this->eventEntity->getCompetition()
                        ->getId(),
                    'year' => $this->eventEntity->getYear(),
                ],
            ]
        );
    }

    private function createFailingResponse(Exception $e): void
    {
        $this->response = new CreateDrlEventResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_NOT_CREATED,
                Response::RESPONSE_MESSAGE => 'Unable to create event',
                Response::RESPONSE_DATA => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ],
            ]
        );
    }
}