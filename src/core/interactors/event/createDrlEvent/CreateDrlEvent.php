<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\createDrlEvent;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\managers\TransactionManagerInterface;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;
use Exception;

/**
 * Class CreateDrlEvent
 * @package DrlArchive\core\interactors\event\createDrlEvent
 * @property CreateDrlEventRequest $request
 */
class CreateDrlEvent extends Interactor
{

    private EventRepositoryInterface $eventRepository;
    private TransactionManagerInterface $transactionManager;
    private DrlEventEntity $eventEntity;

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
        $this->checkUserIsAuthorised(
            SecurityRepositoryInterface::ADD_NEW_PERMISSION
        );
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
        $this->eventRepository->insertDrlEvent(
            $this->eventEntity
        );
    }

    private function createResponse(): void
    {
        $this->response = new CreateDrlEventResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::MESSAGE => 'Succesfully created event',
                Response::DATA => [
                    CreateDrlEventResponse::DATA_DRL_EVENT_ID =>
                        $this->eventEntity->getId(),
                    CreateDrlEventResponse::DATA_LOCATION_ID =>
                        $this->eventEntity->getLocation()->getId(),
                    CreateDrlEventResponse::DATA_COMPETITION_ID =>
                        $this->eventEntity->getCompetition()->getId(),
                    CreateDrlEventResponse::DATA_YEAR =>
                        $this->eventEntity->getYear(),
                ],
            ]
        );
    }

    private function createFailingResponse(Exception $e): void
    {
        $this->response = new CreateDrlEventResponse(
            [
                Response::STATUS => Response::STATUS_NOT_CREATED,
                Response::MESSAGE => 'Unable to create event',
                Response::DATA => [
                    Response::DATA_CODE => $e->getCode(),
                    Response::DATA_MESSAGE => $e->getMessage(),
                ],
            ]
        );
    }
}
