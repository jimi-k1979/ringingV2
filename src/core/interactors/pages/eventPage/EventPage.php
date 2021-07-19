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
                'position' => $result->getPosition(),
                'pealNumber' => $result->getPealNumber(),
                'team' => $result->getTeam()->getName(),
                'teamId' => $result->getTeam()->getId(),
                'faults' => $result->getFaults()
            ];
        }

        foreach ($this->event->getJudges() as $judge) {
            $eventJudges[] = [
                'id' => $judge->getId(),
                'name' => $judge->getFullName(),
            ];
        }

        foreach ($this->winningTeam as $ringer) {
            $eventRingers[] = [
                'id' => $ringer->getRinger()->getId(),
                'name' => $ringer->getRinger()->getFullName(),
                'bell' => $ringer->getBell(),
            ];
        }

        $isUnusualTower = !$this->event->getCompetition()->isSingleTowerCompetition()
            || $this->event->isUnusualTower();

        $data = [
            'eventId' => $this->event->getId(),
            'eventYear' => $this->event->getYear(),
            'eventLocation' => $this->event->getLocation()->getLocation(),
            'isUnusualLocation' => $isUnusualTower,
            'competitionName' => $this->event->getCompetition()->getName(),
            'results' => $eventResults,
            'judges' => $eventJudges,
            'winningTeam' => $eventRingers,
            'statistics' => [
                'totalFaults' => $this->event->getTotalFaults(),
                'meanFaults' => $this->event->getMeanFaults(),
                'winningMargin' => $this->event->getWinningMargin(),
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
