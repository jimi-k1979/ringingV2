<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\checkDrlEventExists;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use Throwable;

/**
 * Class CheckDrlEventExists
 * @package DrlArchive\core\interactors\event\checkDrlEventExists
 * @property CheckDrlEventExistsRequest $request
 */
class CheckDrlEventExists extends Interactor
{

    private EventRepositoryInterface $eventRepository;
    private CompetitionRepositoryInterface $competitionRepository;
    private DrlEventEntity $event;
    private DrlCompetitionEntity $competition;

    public function setEventRepository(
        EventRepositoryInterface $repository
    ): void {
        $this->eventRepository = $repository;
    }

    public function setCompetitionRepository(
        CompetitionRepositoryInterface $repository
    ): void {
        $this->competitionRepository = $repository;
    }

    /**
     * @throws AccessDeniedException
     */
    public function execute(): void
    {
//        $this->checkUserIsAuthorised(
//            SecurityRepositoryInterface::ADD_NEW_PERMISSION
//        );
        try {
            $this->checkEventExists();
            $this->createEventExistsResponse();
        } catch (RepositoryNoResultsException $e) {
            try {
                $this->fetchCompetitionDetails();
                $this->createCompetitionExistsResponse();
            } catch (Throwable $e) {
                $this->createFailingResponse($e);
            }
        } catch (Throwable $e) {
            $this->createFailingResponse($e);
        }
        $this->sendResponse();
    }

    /**
     * @throws CleanArchitectureException
     */
    private function checkEventExists(): void
    {
        $this->event = $this->eventRepository
            ->fetchDrlEventByYearAndCompetitionName(
                $this->request->getEventYear(),
                $this->request->getCompetitionName()
            );
    }

    private function createEventExistsResponse(): void
    {
        $this->response = new CheckDrlEventExistsResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => [
                    'eventId' => $this->event->getId(),
                    'year' => $this->event->getYear(),
                    'competition' => $this->event->getCompetition()->getName(),
                    'location' => $this->event->getLocation()->getLocation(),
                ],
            ]
        );
    }

    /**
     * @throws CleanArchitectureException
     */
    private function fetchCompetitionDetails(): void
    {
        $this->competition = $this->competitionRepository
            ->fetchDrlCompetitionByName(
                $this->request->getCompetitionName()
            );
    }

    private function createCompetitionExistsResponse()
    {
        if ($this->competition->isSingleTowerCompetition()) {
            $data = [
                'competitionId' => $this->competition->getId(),
                'competitionName' => $this->competition->getName(),
                'singleTower' => $this->competition->isSingleTowerCompetition(),
                'usualLocation' => $this->competition->getUsualLocation()->getLocation(),
                'usualLocationId' => $this->competition->getUsualLocation()->getId(),
            ];
        } else {
            $data = [
                'competitionId' => $this->competition->getId(),
                'competitionName' => $this->competition->getName(),
                'singleTower' => $this->competition->isSingleTowerCompetition(),
                'usualLocation' => null,
                'usualLocationId' => null,
            ];
        }

        $this->response = new CheckDrlEventExistsResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => $data,
            ]
        );
    }

    private function createFailingResponse(Throwable $e): void
    {
        if ($e->getCode() === CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION) {
            $status = Response::STATUS_NOT_FOUND;
            $message = 'No competition found';
        } else {
            $status = Response::STATUS_UNKNOWN_ERROR;
            $message = 'Unknown error';
        }

        $this->response = new CheckDrlEventExistsResponse(
            [
                Response::RESPONSE_STATUS => $status,
                Response::RESPONSE_MESSAGE => $message,
                Response::RESPONSE_DATA => [
                    'code' => $e->getCode(),
                ],
            ]
        );
    }
}