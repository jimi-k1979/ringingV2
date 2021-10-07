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

/**
 * Class ReadDrlEvent
 * @package DrlArchive\core\interactors\event\readDrlEvent
 * @property ReadDrlEventRequest $request
 */
class ReadDrlEvent extends Interactor
{
    private EventRepositoryInterface $eventRepository;
    private ResultRepositoryInterface $resultRepository;
    private DrlEventEntity $eventEntity;
    /**
     * @var DrlResultEntity[]
     */
    private array $resultsArray;

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
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::DATA => $responseArray,
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
                    Response::STATUS => Response::STATUS_SUCCESS,
                    Response::DATA =>
                        $this->putEventIntoResponseArray(),
                ]
            );
        } else {
            $this->response = new ReadDrlEventResponse(
                [
                    Response::STATUS => Response::STATUS_NOT_FOUND,
                    Response::MESSAGE => 'Event not found',
                ]
            );
        }
    }

    /**
     * @return array
     */
    private function putEventIntoResponseArray(): array
    {
        return [
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
    }


}