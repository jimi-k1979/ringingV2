<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\newEventPage;


use DrlArchive\core\classes\Response;
use DrlArchive\core\Constants;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\Exceptions\BadDataException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\managers\TransactionManagerInterface;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use Throwable;

/**
 * Class NewEventPage
 * @package DrlArchive\core\interactors\event\newEventPage
 * @property NewEventPageRequest $request
 */
class NewEventPage extends Interactor
{
    public const POSITION = 'position';
    public const FAULTS = 'faults';
    public const TEAM_NAME = 'teamName';
    public const PEAL_NUMBER = 'pealNumber';

    public const YEAR_OUTSIDE_RANGE_EXCEPTION_CODE = 6001;
    public const EVENT_EXISTS_EXCEPTION_CODE = 6002;
    /**
     * @var DrlResultEntity[]
     */
    protected array $results = [];
    protected DrlEventEntity $event;
    private TeamRepositoryInterface $teamRepository;
    private EventRepositoryInterface $eventRepository;
    private ResultRepositoryInterface $resultsRepository;
    private TransactionManagerInterface $transactionManager;

    public function setTeamRepository(
        TeamRepositoryInterface $repository
    ): void {
        $this->teamRepository = $repository;
    }

    public function setEventRepository(
        EventRepositoryInterface $repository
    ): void {
        $this->eventRepository = $repository;
    }

    public function setResultRepository(
        ResultRepositoryInterface $repository
    ): void {
        $this->resultsRepository = $repository;
    }

    public function setTransactionManager(
        TransactionManagerInterface $manager
    ): void {
        $this->transactionManager = $manager;
    }

    public function execute(): void
    {
        try {
            $this->checkUserIsAuthorised(
                SecurityRepositoryInterface::ADD_NEW_PERMISSION
            );

            if (isset($this->request)) {
                $this->transactionManager->startTransaction();
                $this->preProcessData();
                $this->processData();
            }
            $this->createResponse();
            $this->transactionManager->commitTransaction();
        } catch (Throwable $e) {
            $this->transactionManager->rollbackTransaction();
            $this->createFailureResponse($e);
        }
        $this->sendResponse();
    }

    /**
     * @throws CleanArchitectureException
     */
    private function preProcessData(): void
    {
        $this->event = new DrlEventEntity();
        $this->event->setLocation(new LocationEntity());
        $this->event->setCompetition(new DrlCompetitionEntity());

        $year = (int)$this->request->getYear();
        if (
            $year < Constants::MINIMUM_YEAR
            || $year > (int)date('Y')
        ) {
            throw new BadDataException(
                'Year outside range',
                self::YEAR_OUTSIDE_RANGE_EXCEPTION_CODE
            );
        } else {
            $this->event->setYear($this->request->getYear());
        }

        $this->event->getCompetition()->setId(
            $this->request->getCompetitionId()
        );
        $this->event->getLocation()->setId(
            $this->request->getLocationId()
        );

        if (
            $this->request->getUsualLocation() === null
            || $this->request->getUsualLocation() === $this->request->getLocationId()
        ) {
            $this->event->setUnusualTower(false);
        } else {
            $this->event->setUnusualTower(true);
        }

        foreach ($this->request->getResults() as $result) {
            $resultEntity = new DrlResultEntity();
            $resultEntity->setPosition($result[self::POSITION]);
            $resultEntity->setFaults($result[self::FAULTS]);
            $resultEntity->setPealNumber($result[self::PEAL_NUMBER]);
            $resultEntity->setTeam(
                $this->teamRepository->fetchTeamByName(
                    $result[self::TEAM_NAME]
                )
            );

            $this->results[] = $resultEntity;
        }
    }

    /**
     * @throws CleanArchitectureException
     */
    private function processData(): void
    {
        try {
            $this->eventRepository->fetchDrlEventByYearAndCompetitionId(
                $this->event->getYear(),
                $this->event->getCompetition()->getId()
            );
            throw new BadDataException(
                'Event is already in the database',
                self::EVENT_EXISTS_EXCEPTION_CODE
            );
        } catch (RepositoryNoResultsException $e) {
            unset($e);
            $this->eventRepository->insertDrlEvent(
                $this->event
            );
            $this->calculatePoints();
            foreach ($this->results as $result) {
                $this->resultsRepository->insertDrlResult($result);
            }
        }
    }

    private function calculatePoints(): void
    {
        $numberOfTeams = count($this->results);
        $previousFaults = 0.0;
        $firstDrawingTeam = -1;
        $sharedPoints = 0;
        foreach ($this->results as $i => $result) {
            $result->setEvent($this->event);
            if ($result->getFaults() < 0) {
                $result->setPoints(0);
                $result->setPosition($numberOfTeams);
                $result->setFaults(300);
            } else {
                $pointsForThisPosition = ($numberOfTeams - ($i + 1)) * 2;

                if ($result->getFaults() === $previousFaults) {
                    $firstDrawingTeam = $firstDrawingTeam === -1
                        ? $i - 1 : $firstDrawingTeam;
                    $sharedPoints += $pointsForThisPosition;
                } else {
                    if ($firstDrawingTeam !== -1) {
                        $pointsForDraw =
                            $sharedPoints / ($i - $firstDrawingTeam);
                        for ($j = $i - 1; $j >= $firstDrawingTeam; $j--) {
                            $this->results[$j]->setPoints($pointsForDraw);
                        }
                        $firstDrawingTeam = -1;
                        $sharedPoints = $pointsForThisPosition;
                    } else {
                        $sharedPoints = $pointsForThisPosition;
                    }
                    $result->setPoints($pointsForThisPosition);
                    $previousFaults = $result->getFaults();
                }
            }
        }
    }

    private function createResponse(): void
    {
        $this->response = new NewEventPageResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setLoggedInUser($this->loggedInUser);
        if (isset($this->event)) {
            $this->response->setData(
                [
                    NewEventPageResponse::DATA_EVENT_ID =>
                        $this->event->getId(),
                ]
            );
        }
    }

    private function createFailureResponse(Throwable $e): void
    {
        $this->response = new NewEventPageResponse();
        if ($e instanceof AccessDeniedException) {
            $this->response->setStatus(
                Response::STATUS_FORBIDDEN
            );
        }

        $this->response->setMessage("{$e->getCode()}: {$e->getMessage()}");
    }
}
