<?php

namespace DrlArchive\core\interactors\pages\recordsPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\Constants;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\RecordRequestOptionsEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\core\interfaces\repositories\RingerRepositoryInterface;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;

class RecordsPage extends Interactor
{
    private CompetitionRepositoryInterface $competitionRepository;
    private EventRepositoryInterface $eventRepository;
    private TeamRepositoryInterface $teamRepository;
    private RingerRepositoryInterface $ringerRepository;
    /**
     * @var DrlEventEntity[][]
     */
    private array $eventRecords = [];
    private RecordRequestOptionsEntity $recordOptions;

    /**
     * @param CompetitionRepositoryInterface $competitionRepository
     */
    public function setCompetitionRepository(
        CompetitionRepositoryInterface $competitionRepository
    ): void {
        $this->competitionRepository = $competitionRepository;
    }

    /**
     * @param EventRepositoryInterface $eventRepository
     */
    public function setEventRepository(
        EventRepositoryInterface $eventRepository
    ): void {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param TeamRepositoryInterface $teamRepository
     */
    public function setTeamRepository(
        TeamRepositoryInterface $teamRepository
    ): void {
        $this->teamRepository = $teamRepository;
    }

    /**
     * @param RingerRepositoryInterface $ringerRepository
     */
    public function setRingerRepository(
        RingerRepositoryInterface $ringerRepository
    ): void {
        $this->ringerRepository = $ringerRepository;
    }


    public function execute(): void
    {
        try {
            $this->getUserDetails();
            $this->fetchEventRecords();
        } catch (\Throwable $e) {
        }
        $this->response = new Response();
        $this->sendResponse();
    }

    /**
     * @return void
     * @throws CleanArchitectureException
     */
    private function fetchEventRecords(): void
    {
        $this->recordOptions = new RecordRequestOptionsEntity();
        $this->recordOptions->setNumberOfEventsFilter(
            Constants::MAX_RECORDS_TO_DISPLAY_ON_SUMMARY
        );
        $this->recordOptions->setOrderBy(
            Constants::RECORDS_SORT_DESCENDING
        );
        $this->eventRecords[RecordsPageResponse::EVENTS_HIGHEST_ENTRY] =
            $this->eventRepository->fetchDrlEventListByEntry(
                $this->recordOptions
            );
        $this->eventRecords[RecordsPageResponse::EVENTS_HIGHEST_MEAN_FAULTS] =
            $this->eventRepository->fetchDrlEventListByMeanFaults(
                $this->recordOptions
            );
        $this->eventRecords[RecordsPageResponse::EVENTS_HIGHEST_TOTAL_FAULTS] =
            $this->eventRepository->fetchDrlEventListByTotalFaults(
                $this->recordOptions
            );
        $this->eventRecords[RecordsPageResponse::EVENTS_LARGEST_VICTORY_MARGIN] =
            $this->eventRepository->fetchDrlEventsListByVictoryMargin(
                $this->recordOptions
            );

        $this->recordOptions->setOrderBy(
            Constants::RECORDS_SORT_ASCENDING
        );
        $this->eventRecords[RecordsPageResponse::EVENTS_LOWEST_MEAN_FAULTS] =
            $this->eventRepository->fetchDrlEventListByMeanFaults(
                $this->recordOptions
            );
        $this->eventRecords[RecordsPageResponse::EVENTS_LOWEST_TOTAL_FAULTS] =
            $this->eventRepository->fetchDrlEventListByTotalFaults(
                $this->recordOptions
            );
        $this->eventRecords[RecordsPageResponse::EVENTS_SMALLEST_VICTORY_MARGIN] =
            $this->eventRepository->fetchDrlEventsListByVictoryMargin(
                $this->recordOptions
            );
    }
}
