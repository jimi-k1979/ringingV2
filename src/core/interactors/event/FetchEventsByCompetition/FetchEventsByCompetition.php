<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchEventsByCompetition;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\AbstractEventEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use Exception;

/**
 * Class FetchEventsByCompetition
 * @package DrlArchive\core\interactors\event\FetchEventsByCompetition
 * @property FetchEventsByCompetitionRequest $request
 */
class FetchEventsByCompetition extends Interactor
{

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var AbstractEventEntity[]
     */
    private $eventList;

    public function setEventRepository(EventRepositoryInterface $repository)
    {
        $this->eventRepository = $repository;
    }

    public function execute(): void
    {
        $this->checkUserIsAuthorised();
        try {
            $this->fetchCompetitionList();
            $this->createSuccessfulResponse();
        } catch (Exception $e) {
            $this->createFailureResponse($e);
        }
        $this->sendResponse();
    }

    private function fetchCompetitionList(): void
    {
        if (
            $this->request->getCompetitionType() ===
            AbstractCompetitionEntity::COMPETITION_TYPE_DRL
        ) {
            $this->eventList = $this->eventRepository
                ->fetchDrlEventsByCompetitionId(
                    $this->request->getCompetitionId()
                );
        } else {
            throw new GeneralRepositoryErrorException(
                'Invalid event type',
                EventRepositoryInterface::INVALID_EVENT_TYPE_EXCEPTION
            );
        }
    }

    private function createSuccessfulResponse(): void
    {
        $dataArray = [];
        foreach ($this->eventList as $eventEntity) {
            $dataArray[] = [
                'text' => $eventEntity->getYear(),
                'id' => $eventEntity->getId(),
            ];
        }

        $this->response = new FetchEventsByCompetitionResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => $dataArray
            ]
        );
    }

    private function createFailureResponse(Exception $e): void
    {
        $this->response = new FetchEventsByCompetitionResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_NOT_FOUND,
                Response::RESPONSE_MESSAGE => 'No events found for that competition id',
                Response::RESPONSE_DATA => [
                    'code' => $e->getCode()
                ],
            ]
        );
    }

}