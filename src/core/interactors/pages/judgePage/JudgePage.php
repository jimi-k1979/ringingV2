<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\judgePage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\Exceptions\BadDataException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;

/**
 * @property JudgePageRequest $request
 */
class JudgePage extends Interactor
{
    private JudgeRepositoryInterface $judgeRepository;
    private JudgeEntity $judgeEntity;
    /**
     * @var DrlEventEntity[]
     */
    private array $eventList = [];

    public function setJudgeRepository(
        JudgeRepositoryInterface $create
    ): void {
        $this->judgeRepository = $create;
    }

    public function execute(): void
    {
        try {
            $this->getUserDetails();
            $this->checkForRequestData();
            $this->fetchJudgeDetails();
            $this->fetchEventList();
            $this->createResponse();
        } catch (\Throwable $e) {
            $this->createFailingResponse($e);
        }
        $this->sendResponse();
    }

    /**
     * @throws BadDataException
     */
    private function checkForRequestData(): void
    {
        if (empty($this->request->getJudgeId())) {
            throw new BadDataException(
                'No judge id given'
            );
        }
    }

    private function createFailingResponse(\Throwable $e): void
    {
        $this->response = new JudgePageResponse();
        $this->response->setStatus(Response::STATUS_BAD_REQUEST);
        $this->response->setMessage($e->getMessage());
    }

    /**
     * @throws CleanArchitectureException
     */
    private function fetchJudgeDetails(): void
    {
        $this->judgeEntity = $this->judgeRepository->fetchJudgeById(
            $this->request->getJudgeId()
        );
    }

    private function fetchEventList(): void
    {
        $this->eventList = $this->judgeRepository->fetchJudgeDrlEventList(
            $this->judgeEntity
        );
    }

    private function createResponse(): void
    {
        $data = [
            JudgePageResponse::DATA_JUDGE => [
                JudgePageResponse::DATA_JUDGE_ID =>
                    $this->judgeEntity->getId(),
                JudgePageResponse::DATA_JUDGE_FIRST_NAME =>
                    $this->judgeEntity->getFirstName(),
                JudgePageResponse::DATA_JUDGE_LAST_NAME =>
                    $this->judgeEntity->getLastName(),
                JudgePageResponse::DATA_JUDGE_RINGER_ID =>
                    $this->judgeEntity->getRinger()->getId()
            ],
            JudgePageResponse::DATA_EVENTS => [],
            JudgePageResponse::DATA_STATS => [
                JudgePageResponse::DATA_STATS_NO_OF_EVENTS =>
                    count($this->eventList)
            ]
        ];

        foreach ($this->eventList as $event) {
            if (
                !$event->getCompetition()->isSingleTowerCompetition()
                || $event->isUnusualTower()
            ) {
                $eventName = sprintf(
                    '%s @ %s',
                    $event->getCompetition()->getName(),
                    $event->getLocation()->getLocation(),
                );
            } else {
                $eventName = $event->getCompetition()->getName();
            }
            $eventArray = [
                JudgePageResponse::DATA_EVENT_ID =>
                    $event->getId(),
                JudgePageResponse::DATA_EVENT_YEAR =>
                    $event->getYear(),
                JudgePageResponse::DATA_EVENT_EVENT =>
                    $eventName,
            ];
            $data[JudgePageResponse::DATA_EVENTS][] = $eventArray;
        }

        $this->response = new JudgePageResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::DATA => $data,
            ]
        );
    }
}
