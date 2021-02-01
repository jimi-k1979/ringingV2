<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\FetchDrlEventAndResults;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use Exception;

/**
 * Class FetchDrlEventAndResults
 * @package DrlArchive\core\interactors\event\FetchDrlEventAndResults
 * @property FetchDrlEventAndResultsRequest $request
 */
class FetchDrlEventAndResults extends Interactor
{

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;
    /**
     * @var JudgeRepositoryInterface
     */
    private $judgeRepository;
    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;
    /**
     * @var DrlEventEntity
     */
    private $event;
    /**
     * @var DrlResultEntity[]
     */
    private $results;
    /**
     * @var null|JudgeEntity[]
     */
    private $judges;

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

    public function setLocationRepository(
        LocationRepositoryInterface $repository
    ): void {
        $this->locationRepository = $repository;
    }

    public function execute(): void
    {
        $this->checkUserIsAuthorised();

        try {
            $this->fetchEventDetails();
            $this->fetchResults();
            $this->fetchJudges();
            $this->createSuccessfulResponse();
        } catch (Exception $e) {
            $this->createFailureResponse($e);
        }
        $this->sendResponse();
    }

    private function fetchEventDetails(): void
    {
        $this->event = $this->eventRepository->fetchDrlEvent(
            $this->request->getEventId()
        );
        $this->event->setLocation(
            $this->locationRepository->fetchLocationById(
                $this->event->getLocation()->getId()
            )
        );
    }

    private function fetchResults(): void
    {
        $this->results = $this->resultRepository->fetchDrlEventResults(
            $this->event
        );
    }

    private function fetchJudges(): void
    {
        try {
            $this->judges = $this->judgeRepository->fetchJudgesByDrlEvent(
                $this->event
            );
        } catch (RepositoryNoResultsException $e) {
            $this->judges = null;
        }
    }

    private function createSuccessfulResponse(): void
    {
        $dataArray = [
            'event' => [
                'year' => $this->event->getYear(),
                'competition' => $this->event->getCompetition()->getName(),
                'singleTower' => $this->event->getCompetition()
                    ->isSingleTowerCompetition(),
                'location' => $this->event->getLocation()->getLocation(),
                'unusualTower' => $this->event->isUnusualTower(),
            ],
            'results' => [],
            'judges' => [],
        ];

        foreach ($this->results as $index => $result) {
            if ($index === 0) {
                if (empty($result->getPealNumber())) {
                    $pealNumbers = false;
                } else {
                    $pealNumbers = true;
                }
            }
            if ($pealNumbers) {
                $dataArray['results'][] = [
                    'position' => $result->getPosition(),
                    'peal number' => $result->getPealNumber(),
                    'team' => $result->getTeam()->getName(),
                    'faults' => $result->getFaults(),
                ];
            } else {
                $dataArray['results'][] = [
                    'position' => $result->getPosition(),
                    'team' => $result->getTeam()->getName(),
                    'faults' => $result->getFaults(),
                ];
            }
        }

        if ($this->judges !== null) {
            foreach ($this->judges as $judge) {
                $dataArray['judges'][] = [
                    'name' => $judge->getFullName()
                ];
            }
        }

        $this->response = new FetchDrlEventAndResultsResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => $dataArray,
            ]
        );
    }

    private function createFailureResponse(Exception $e): void
    {
        if (
            $e->getCode() ===
            ResultRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
        ) {
            $dataArray = [
                'code' => $e->getCode(),
                'event' => [
                    'year' => $this->event->getYear(),
                    'competition' => $this->event->getCompetition()->getName(),
                    'singleTower' => $this->event->getCompetition()
                        ->isSingleTowerCompetition(),
                    'location' => $this->event->getLocation()->getLocation(),
                    'unusualTower' => $this->event->isUnusualTower(),
                ],
            ];
            $message = 'No results found';
        } else {
            $dataArray = [
                'code' => $e->getCode()
            ];
            $message = 'No event data';
        }

        $this->response = new FetchDrlEventAndResultsResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_NOT_FOUND,
                Response::RESPONSE_MESSAGE => $message,
                Response::RESPONSE_DATA => $dataArray,
            ]
        );
    }
}
