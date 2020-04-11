<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\readDrlEvent;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use Exception;

class ReadDrlEvent extends Interactor
{
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;
    /**
     * @var DrlEventEntity
     */
    private $eventEntity;
    /**
     * @var DrlResultEntity[]
     */
    private $resultsArray;

    /**
     * @param EventRepositoryInterface $eventRepository
     */
    public function setEventRepository(
        EventRepositoryInterface $eventRepository
    ): void {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param ResultRepositoryInterface $resultRepository
     */
    public function setResultRepository(
        ResultRepositoryInterface $resultRepository
    ): void {
        $this->resultRepository = $resultRepository;
    }


    public function execute(): void
    {
        $this->checkUserIsAuthorised();

        try {
            $this->fetchEventData();
            $this->fetchEventResults();
            $this->createResponse();
        } catch (Exception $e) {
            $this->createFailingResponse($e);
        }

        $this->sendResponse();
    }

    private function fetchEventData(): void
    {
        $this->eventEntity = $this->eventRepository->fetchDrlEvent(
            $this->request->getEventId()
        );
    }

    private function fetchEventResults(): void
    {
        $this->resultsArray = $this->resultRepository->fetchDrlEventResults(
            $this->eventEntity
        );
    }

    private function createResponse(): void
    {
        $responseArray = $this->putEventIntoResponseArray();

        foreach ($this->resultsArray as $result) {
            $responseArray['event']['results'][] = [
                'position' => $result->getPosition(),
                'pealNumber' => $result->getPealNumber(),
                'faults' => $result->getFaults(),
                'team' => $result->getTeam()->getName(),
                'points' => $result->getPoints(),
            ];
        }
        $this->response = new ReadDrlEventResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => $responseArray,
            ]
        );
    }

    private function createFailingResponse(Exception $e): void
    {
        if (
            $e->getCode() === ResultRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
        ) {
            $this->response = new ReadDrlEventResponse(
                [
                    Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                    Response::RESPONSE_DATA =>
                        $this->putEventIntoResponseArray(),
                ]
            );
        } else {
            $this->response = new ReadDrlEventResponse(
                [
                    Response::RESPONSE_STATUS => Response::STATUS_NOT_FOUND,
                    Response::RESPONSE_MESSAGE => 'Event not found',
                ]
            );
        }
    }

    /**
     * @return array
     */
    private function putEventIntoResponseArray(): array
    {
        $responseArray = [
            'event' => [
                'id' => $this->eventEntity->getId(),
                'competition' =>
                    $this->eventEntity->getCompetition()->getName(),
                'location' =>
                    $this->eventEntity->getLocation()->getLocation(),
                'year' => $this->eventEntity->getYear(),
                'results' => [],
            ],
        ];
        return $responseArray;
    }


}