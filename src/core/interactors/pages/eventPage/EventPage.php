<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\eventPage;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\entities\WinningRingerEntity;
use DrlArchive\core\Exceptions\BadDataException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use DrlArchive\core\interfaces\repositories\RingerRepositoryInterface;

/**
 * Class EventPage
 * @package DrlArchive\core\interactors\pages\eventPage
 * @property EventPageRequest $request
 */
class EventPage extends Interactor
{

    public const NO_EVENT_ID_EXCEPTION_CODE = 1234;
    private EventRepositoryInterface $eventRepository;
    private ResultRepositoryInterface $resultRepository;
    private JudgeRepositoryInterface $judgeRepository;
    private RingerRepositoryInterface $ringerRepository;
    private DrlEventEntity $event;
    /**
     * @var DrlResultEntity[]
     */
    private array $results;
    /**
     * @var WinningRingerEntity[]
     */
    private array $winningTeam;

    /**
     * @param EventRepositoryInterface $repository
     */
    public function setEventRepository(
        EventRepositoryInterface $repository
    ): void {
        $this->eventRepository = $repository;
    }

    public function setResultRepository(
        ResultRepositoryInterface $repository
    ): void {
        $this->resultRepository = $repository;
    }

    public function setJudgeRepository(
        JudgeRepositoryInterface $repository
    ): void {
        $this->judgeRepository = $repository;
    }

    public function setRingerRepository(
        RingerRepositoryInterface $repository
    ): void {
        $this->ringerRepository = $repository;
    }

    public function execute(): void
    {
        try {
            $this->getUserDetails();
            $this->checkRequestData();
            $this->fetchEventDetails();
            $this->createResponse();
        } catch (CleanArchitectureException $e) {
            $this->createFailingResponse($e);
        }

        $this->sendResponse();
    }

    /**
     * @throws BadDataException
     */
    private function checkRequestData(): void
    {
        if ($this->request->getEventId() === 0) {
            throw new BadDataException(
                'No event id given',
                self::NO_EVENT_ID_EXCEPTION_CODE
            );
        }
    }

    /**
     * @throws CleanArchitectureException
     */
    private function fetchEventDetails(): void
    {
        $this->event = $this->eventRepository->fetchDrlEvent(
            $this->request->getEventId()
        );
        $this->results = $this->resultRepository->fetchDrlEventResults(
            $this->event
        );
        $this->event->setJudges(
            $this->judgeRepository->fetchJudgesByDrlEvent(
                $this->event
            )
        );
        $this->fetchWinningTeam();
        $this->fetchStatistics();
    }

    private function createResponse(): void
    {
        $eventResults = [];
        $eventJudges = [];
        $eventRingers = [];

        foreach ($this->results as $result) {
            $eventResults[] = [
                EventPageResponse::DATA_RESULTS_POSITION =>
                    $result->getPosition(),
                EventPageResponse::DATA_RESULTS_PEAL_NUMBER =>
                    $result->getPealNumber(),
                EventPageResponse::DATA_RESULTS_TEAM =>
                    $result->getTeam()->getName(),
                EventPageResponse::DATA_RESULTS_TEAM_ID =>
                    $result->getTeam()->getId(),
                EventPageResponse::DATA_RESULTS_FAULTS =>
                    $result->getFaults(),
            ];
        }

        foreach ($this->event->getJudges() as $judge) {
            $eventJudges[] = [
                EventPageResponse::DATA_JUDGES_ID =>
                    $judge->getId(),
                EventPageResponse::DATA_JUDGES_NAME =>
                    $judge->getFullName(),
            ];
        }

        foreach ($this->winningTeam as $ringer) {
            $eventRingers[] = [
                EventPageResponse::DATA_WINNING_TEAM_ID =>
                    $ringer->getRinger()->getId(),
                EventPageResponse::DATA_WINNING_TEAM_NAME =>
                    $ringer->getRinger()->getFullName(),
                EventPageResponse::DATA_WINNING_TEAM_BELL =>
                    $ringer->getBell(),
            ];
        }

        $isUnusualTower = !$this->event->getCompetition()->isSingleTowerCompetition()
            || $this->event->isUnusualTower();

        $data = [
            EventPageResponse::DATA_EVENT_ID =>
                $this->event->getId(),
            EventPageResponse::DATA_EVENT_YEAR =>
                $this->event->getYear(),
            EventPageResponse::DATA_EVENT_LOCATION =>
                $this->event->getLocation()->getLocation(),
            EventPageResponse::DATA_IS_UNUSUAL_LOCATION =>
                $isUnusualTower,
            EventPageResponse::DATA_COMPETITION_NAME =>
                $this->event->getCompetition()->getName(),
            EventPageResponse::DATA_RESULTS =>
                $eventResults,
            EventPageResponse::DATA_JUDGES =>
                $eventJudges,
            EventPageResponse::DATA_WINNING_TEAM =>
                $eventRingers,
            EventPageResponse::DATA_STATS => [
                EventPageResponse::DATA_STATS_TOTAL_FAULTS =>
                    $this->event->getTotalFaults(),
                EventPageResponse::DATA_STATS_MEAN_FAULTS =>
                    $this->event->getMeanFaults(),
                EventPageResponse::DATA_STATS_WINNING_MARGIN =>
                    $this->event->getWinningMargin(),
            ],
        ];

        $this->response = new EventPageResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setLoggedInUser($this->loggedInUser);
        $this->response->setData($data);
    }

    private function createFailingResponse(CleanArchitectureException $e): void
    {
        $this->response = new EventPageResponse();
        $this->response->setStatus(Response::STATUS_BAD_REQUEST);
        $this->response->setMessage($e->getMessage());
    }

    private function fetchWinningTeam(): void
    {
        $this->winningTeam = $this->ringerRepository->fetchWinningTeamByEvent(
            $this->event
        );
    }

    private function fetchStatistics(): void
    {
        $this->eventRepository->fetchSingleDrlEventStatistics($this->event);
    }

}
