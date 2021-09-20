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

    private RingerRepositoryInterface $ringerRepository;
    private RingerEntity $ringer;
    /**
     * @var WinningRingerEntity[]
     */
    private array $eventsList;

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
            'ringer' => [
                'id' => $this->ringer->getId(),
                'firstName' => $this->ringer->getFirstName(),
                'lastName' => $this->ringer->getLastName(),
                'notes' => $this->ringer->getNotes(),
                'judgeId' => $this->ringer->getJudgeId(),
            ],
            'events' => [],
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
            $data['events'][] = [
                'id' => $event->getEvent()->getId(),
                'year' => $event->getEvent()->getYear(),
                'event' => $eventName,
            ];
        }

        $this->response = new RingerPageResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setData($data);
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
    }


}
