<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\ringerPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\entities\WinningRingerEntity;
use DrlArchive\core\Exceptions\BadDataException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\RingerRepositoryInterface;

/**
 * @property RingerPageRequest $request
 */
class RingerPage extends Interactor
{
    public const NO_RINGER_ID_EXCEPTION_CODE = 1234;

    public const STATS_NO_OF_WINS = 'numberOfWins';
    public const STATS_WINS_BY_BELL = 'winsByBell';
    public const STATS_BELL_TREBLE = 'treble';
    public const STATS_BELL_TENOR = 'tenor';
    public const STATS_BELL_STRAPPER = 'strapper';
    public const STATS_WINS_BY_DECADE = 'winsByDecade';
    public const STATS_WINS_BY_NUMBER_OF_BELLS = 'winsByNumberOfBells';
    public const STATS_WINS_BY_COMPETITION = 'winsByCompetition';

    private RingerRepositoryInterface $ringerRepository;
    private RingerEntity $ringer;
    /**
     * @var WinningRingerEntity[]
     */
    private array $eventsList;
    protected array $stats = [
        self::STATS_NO_OF_WINS => 0,
        self::STATS_WINS_BY_BELL => [
            self::STATS_BELL_TREBLE => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0,
            '6' => 0,
            '7' => 0,
            self::STATS_BELL_TENOR => 0,
            self::STATS_BELL_STRAPPER => 0,
        ],
        self::STATS_WINS_BY_DECADE => [],
        self::STATS_WINS_BY_NUMBER_OF_BELLS => [
            '6' => 0,
            '8' => 0,
        ],
        self::STATS_WINS_BY_COMPETITION => [],
    ];

    public function setRingerRepository(
        RingerRepositoryInterface $repository
    ): void {
        $this->ringerRepository = $repository;
    }

    public function execute(): void
    {
        try {
            $this->getUserDetails();
            $this->checkForEmptyRequest();
            $this->fetchRingerData();
            $this->fetchRingersWinningEvents();
            $this->createResponse();
        } catch (CleanArchitectureException $e) {
            $this->createFailureResponse($e);
        }

        $this->sendResponse();
    }

    /**
     * @throws BadDataException
     */
    private function checkForEmptyRequest(): void
    {
        if ($this->request->getRingerId() === 0) {
            throw new BadDataException(
                'No ringer id given',
                self::NO_RINGER_ID_EXCEPTION_CODE
            );
        }
    }

    private function fetchRingerData(): void
    {
        $this->ringer = $this->ringerRepository->fetchRingerById(
            $this->request->getRingerId()
        );
    }

    private function fetchRingersWinningEvents(): void
    {
        $this->eventsList = $this->ringerRepository
            ->fetchWinningRingerDetailsByRinger($this->ringer);
    }

    private function createResponse(): void
    {
        $data = [
            RingerPageResponse::DATA_RINGER => [
                RingerPageResponse::DATA_RINGER_ID =>
                    $this->ringer->getId(),
                RingerPageResponse::DATA_RINGER_FIRST_NAME =>
                    $this->ringer->getFirstName(),
                RingerPageResponse::DATA_RINGER_LAST_NAME =>
                    $this->ringer->getLastName(),
                RingerPageResponse::DATA_RINGER_NOTES =>
                    $this->ringer->getNotes(),
                RingerPageResponse::DATA_RINGER_JUDGE_ID =>
                    $this->ringer->getJudgeId(),
            ],
            RingerPageResponse::DATA_EVENTS => [],
        ];

        foreach ($this->eventsList as $event) {
            if (
                $event->getEvent()->getCompetition()->isSingleTowerCompetition()
                && !$event->getEvent()->isUnusualTower()
            ) {
                $eventName = $event->getEvent()->getCompetition()->getName();
            } else {
                $eventName = $event->getEvent()->getCompetition()->getName()
                    . ' @ ' . $event->getEvent()->getLocation()->getLocation();
            }
            $data[RingerPageResponse::DATA_EVENTS][] = [
                RingerPageResponse::DATA_EVENT_ID =>
                    $event->getEvent()->getId(),
                RingerPageResponse::DATA_EVENT_YEAR =>
                    $event->getEvent()->getYear(),
                RingerPageResponse::DATA_EVENT_EVENT =>
                    $eventName,
                RingerPageResponse::DATA_EVENT_BELL =>
                    $event->getBell(),
            ];
            $this->updateStatistics($event);
        }
        $data[RingerPageResponse::DATA_STATS] = $this->stats;

        $this->response = new RingerPageResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setData($data);
        $this->response->setLoggedInUser($this->loggedInUser);
    }

    private function createFailureResponse(CleanArchitectureException $e)
    {
        $status = Response::STATUS_UNKNOWN_ERROR;
        $message = 'Unknown error';

        if ($e->getCode() === self::NO_RINGER_ID_EXCEPTION_CODE) {
            $status = Response::STATUS_BAD_REQUEST;
            $message = $e->getMessage();
        }

        $this->response = new RingerPageResponse();
        $this->response->setStatus($status);
        $this->response->setMessage($message);
        $this->response->setLoggedInUser($this->loggedInUser);
    }

    private function updateStatistics(WinningRingerEntity $event): void
    {
        $decade = substr($event->getEvent()->getYear(), 0, 3);
        $noOfBells = $event->getEvent()->getCompetition()->getNumberOfBells();
        $competitionName = $event->getEvent()->getCompetition()->getName();

        $this->stats[self::STATS_NO_OF_WINS]++;
        $this->stats[self::STATS_WINS_BY_NUMBER_OF_BELLS][$noOfBells]++;

        switch ($event->getBell()) {
            case '1':
                $this->stats[self::STATS_WINS_BY_BELL][self::STATS_BELL_TREBLE]++;
                break;

            case '6':
                if ($noOfBells === '6') {
                    $this->stats[self::STATS_WINS_BY_BELL][self::STATS_BELL_TENOR]++;
                } else {
                    $this->stats[self::STATS_WINS_BY_BELL]['6']++;
                }
                break;

            case '8':
                $this->stats[self::STATS_WINS_BY_BELL][self::STATS_BELL_TENOR]++;
                break;

            case self::STATS_BELL_STRAPPER:
                $this->stats[self::STATS_WINS_BY_BELL][self::STATS_BELL_STRAPPER]++;
                break;

            default:
                $this->stats[self::STATS_WINS_BY_BELL][$event->getBell()]++;
        }

        if (isset($this->stats[self::STATS_WINS_BY_DECADE][$decade])) {
            $this->stats[self::STATS_WINS_BY_DECADE][$decade]++;
        } else {
            $this->stats[self::STATS_WINS_BY_DECADE][$decade] = 1;
        }

        if (
            isset($this->stats[self::STATS_WINS_BY_COMPETITION][$competitionName])
        ) {
            $this->stats[self::STATS_WINS_BY_COMPETITION][$competitionName]++;
        } else {
            $this->stats[self::STATS_WINS_BY_COMPETITION][$competitionName] = 1;
        }
    }

}
