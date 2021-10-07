<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchEventsByCompetition;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\AbstractEventEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\interactors\event\FetchDrlEventsByYear\FetchDrlEventsByYearResponse;
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

    private EventRepositoryInterface $eventRepository;
    /**
     * @var AbstractEventEntity[]
     */
    private array $eventList;

    public function setEventRepository(EventRepositoryInterface $repository)
    {
        $this->eventRepository = $repository;
    }

    public function execute(): void
    {
        try {
            $this->checkUserIsAuthorised();
            $this->fetchCompetitionList();
            $this->createSuccessfulResponse();
        } catch (Exception $e) {
            $this->createFailureResponse($e);
        }
        $this->sendResponse();
    }

    /**
     * @throws CleanArchitectureException
     * @throws GeneralRepositoryErrorException
     */
    private function fetchCompetitionList(): void
    {
        if (
            $this->request->getCompetitionType() ===
            AbstractCompetitionEntity::COMPETITION_TYPE_DRL
        ) {
            $this->eventList = $this->eventRepository
                ->fetchDrlEventsByCompetitionName(
                    $this->request->getCompetition()
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
                FetchDrlEventsByYearResponse::DATA_TEXT =>
                    $eventEntity->getYear(),
                FetchDrlEventsByYearResponse::DATA_ID =>
                    $eventEntity->getId(),
            ];
        }

        $this->response = new FetchEventsByCompetitionResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::DATA => $dataArray
            ]
        );
    }

    private function createFailureResponse(Exception $e): void
    {
        $this->response = new FetchEventsByCompetitionResponse(
            [
                Response::STATUS => Response::STATUS_NOT_FOUND,
                Response::MESSAGE => 'No events found for that competition id',
                Response::DATA => [
                    Response::DATA_CODE => $e->getCode()
                ],
            ]
        );
    }

}
