<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\checkDrlEventExists;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;
use Exception;

/**
 * Class CheckDrlEventExists
 * @package DrlArchive\core\interactors\event\checkDrlEventExists
 * @property CheckDrlEventExistsRequest $request
 */
class CheckDrlEventExists extends Interactor
{

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;
    /**
     * @var DrlEventEntity
     */
    private $event;
    /**
     * @var DrlCompetitionEntity
     */
    private $competition;

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
        $this->checkUserIsAuthorised(
            SecurityRepositoryInterface::ADD_NEW_PERMISSION
        );
        try {
            $this->checkEventExists();
            $this->createEventExistsResponse();
        } catch (RepositoryNoResults $e) {
            try {
                $this->fetchCompetitionDetails();
                $this->createCompetitionExistsResponse();
            } catch (Exception $e) {
                $this->createFailingResponse($e);
            }
        } catch (Exception $e) {
        }
        $this->sendResponse();
    }

    /**
     * @throws RepositoryNoResults
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
     * @throws RepositoryNoResults
     * @throws GeneralRepositoryErrorException
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
                'locationId' => $this->competition->getUsualLocation()->getId(),
            ];
        } else {
            $data = [
                'competitionId' => $this->competition->getId(),
                'competitionName' => $this->competition->getName(),
                'singleTower' => $this->competition->isSingleTowerCompetition(),
                'usualLocation' => null,
                'locationId' => null,
            ];
        }

        $this->response = new CheckDrlEventExistsResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => $data,
            ]
        );
    }

    private function createFailingResponse(Exception $e): void
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