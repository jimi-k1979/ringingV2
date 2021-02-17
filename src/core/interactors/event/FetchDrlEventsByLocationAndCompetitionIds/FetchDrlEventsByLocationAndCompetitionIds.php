<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchDrlEventsByLocationAndCompetitionIds;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use Exception;

/**
 * Class FetchDrlEventsByLocationAndCompetitionIds
 * @package DrlArchive\core\interactors\event\FetchDrlEventsByLocationAndCompetitionIds
 * @property FetchDrlEventsByLocationAndCompetitionIdsRequest $request
 */
class FetchDrlEventsByLocationAndCompetitionIds extends Interactor
{

    private EventRepositoryInterface $eventRepository;
    /**
     * @var DrlEventEntity[]
     */
    private array $queryData;

    public function setEventRepository(
        EventRepositoryInterface $repository
    ): void {
        $this->eventRepository = $repository;
    }

    public function execute(): void
    {
        $this->checkUserIsAuthorised();

        try {
            $this->fetchData();
            $this->createResponse();
        } catch (Exception $e) {
            $this->createFailureResponse($e);
        }

        $this->sendResponse();
    }

    private function fetchData(): void
    {
        $this->queryData = $this->eventRepository
            ->fetchDrlEventsByCompetitionAndLocationIds(
                $this->request->getCompetitionId(),
                $this->request->getLocationId()
            );
    }

    private function createResponse(): void
    {
        $dataArray = [];
        foreach ($this->queryData as $queryDatum) {
            $dataArray[] = [
                'id' => $queryDatum->getId(),
                'text' => $queryDatum->getYear(),
            ];
        }

        $this->response = new FetchDrlEventsByLocationAndCompetitionIdsResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => $dataArray,
            ]
        );
    }

    private function createFailureResponse(Exception $e): void
    {
        $this->response = new FetchDrlEventsByLocationAndCompetitionIdsResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_NOT_FOUND,
                Response::RESPONSE_MESSAGE => 'No events found',
                Response::RESPONSE_DATA => [
                    'code' => $e->getCode(),
                ],
            ]
        );
    }


}