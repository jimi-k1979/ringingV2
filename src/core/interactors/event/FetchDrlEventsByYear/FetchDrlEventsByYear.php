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

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var DrlEventEntity[]
     */
    private $data;

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
                'id' => $datum->getId(),
                'name' => $datum->getCompetition()->getName()
            ];
        }
        $this->response = new FetchDrlEventsByYearResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => $dataArray,
            ]
        );
    }

    private function createFailureResponse(Exception $e): void
    {
        $this->response = new FetchDrlEventsByYearResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_NOT_FOUND,
                Response::RESPONSE_MESSAGE => 'No competitions found',
                Response::RESPONSE_DATA => [
                    'code' => $e->getCode(),
                ],
            ]
        );
    }

}