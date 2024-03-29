<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\fetchDrlEventsByCompetitionIdAndLocation;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use Exception;

/**
 * Class FetchDrlEventsByCompetitionIdAndLocation
 * @package DrlArchive\core\interactors\event\FetchDrlEventsByCompetitionIdAndLocation
 * @property FetchDrlEventsByCompetitionIdAndLocationRequest $request
 */
class FetchDrlEventsByCompetitionIdAndLocation extends Interactor
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
            ->fetchDrlEventsByCompetitionIdAndVenue(
                $this->request->getCompetitionId(),
                $this->request->getLocation()
            );
    }

    private function createResponse(): void
    {
        $dataArray = [];
        foreach ($this->queryData as $queryDatum) {
            $dataArray[] = [
                FetchDrlEventsByCompetitionIdAndLocationResponse::DATA_ID =>
                    $queryDatum->getId(),
                FetchDrlEventsByCompetitionIdAndLocationResponse::DATA_TEXT =>
                    $queryDatum->getYear(),
            ];
        }

        $this->response = new FetchDrlEventsByCompetitionIdAndLocationResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::DATA => $dataArray,
            ]
        );
    }

    private function createFailureResponse(Exception $e): void
    {
        $this->response = new FetchDrlEventsByCompetitionIdAndLocationResponse(
            [
                Response::STATUS => Response::STATUS_NOT_FOUND,
                Response::MESSAGE => 'No events found',
                Response::DATA => [
                    Response::DATA_CODE => $e->getCode(),
                ],
            ]
        );
    }


}
