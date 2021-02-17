<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\ringer\RingerFuzzySearch;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\RingerRepositoryInterface;
use Exception;

/**
 * Class RingerFuzzySearch
 * @package DrlArchive\core\interactors\ringer\RingerFuzzySearch
 * @property RingerFuzzySearchRequest $request
 */
class RingerFuzzySearch extends Interactor
{

    private RingerRepositoryInterface $ringerRepository;
    /**
     * @var RingerEntity[]
     */
    private array $ringersList;

    public function setRingerRepository(
        RingerRepositoryInterface $repository
    ): void {
        $this->ringerRepository = $repository;
    }

    public function execute(): void
    {
        $this->checkUserIsAuthorised();
        try {
            $this->searchForRingers();
            $this->createResponse();
        } catch (Exception $e) {
            $this->createFailingResponse();
        }

        $this->sendResponse();
    }

    private function searchForRingers(): void
    {
        $this->ringersList = $this->ringerRepository->fuzzySearchRinger(
            $this->request->getSearchTerm()
        );
    }

    private function createResponse(): void
    {
        $responseArray = [];

        foreach ($this->ringersList as $entity) {
            $responseArray[] = [
                'id' => $entity->getId(),
                'firstName' => $entity->getFirstName(),
                'lastName' => $entity->getLastName(),
                'fullName' => $entity->getFullName()
            ];
        }

        $this->response = new RingerFuzzySearchResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => $responseArray,
            ]
        );
    }

    private function createFailingResponse(): void
    {
        $this->response = new RingerFuzzySearchResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => [],
            ]
        );
    }
}