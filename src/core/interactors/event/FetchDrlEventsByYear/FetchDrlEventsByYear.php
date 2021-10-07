<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchDrlEventsByYear;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use Exception;

/**
 * Class FetchDrlEventsByYear
 * @package DrlArchive\core\interactors\event\FetchDrlEventsByYear
 * @property FetchDrlEventsByYearRequest $request
 */
class FetchDrlEventsByYear extends Interactor
{

    private EventRepositoryInterface $eventRepository;
    /**
     * @var DrlEventEntity[]
     */
    private array $data;

    public function setEventRepository(EventRepositoryInterface $repository): void
    {
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
        $this->data = $this->eventRepository->fetchDrlEventsByYear(
            $this->request->getYear()
        );
    }

    private function createResponse(): void
    {
        $dataArray = [];
        foreach ($this->data as $datum) {
            $dataArray[] = [
                FetchDrlEventsByYearResponse::DATA_ID =>
                    $datum->getId(),
                FetchDrlEventsByYearResponse::DATA_TEXT =>
                    $datum->getCompetition()->getName(),
            ];
        }
        $this->response = new FetchDrlEventsByYearResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::DATA => $dataArray,
            ]
        );
    }

    private function createFailureResponse(Exception $e): void
    {
        $this->response = new FetchDrlEventsByYearResponse(
            [
                Response::STATUS => Response::STATUS_NOT_FOUND,
                Response::MESSAGE => 'No competitions found',
                Response::DATA => [
                    Response::DATA_CODE => $e->getCode(),
                ],
            ]
        );
    }

}
