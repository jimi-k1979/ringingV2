<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\location\locationFuzzySearch;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;

/**
 * Class LocationFuzzySearch
 * @package DrlArchive\core\interactors\location\locationFuzzySearch
 * @property LocationFuzzySearchRequest $request
 */
class LocationFuzzySearch extends Interactor
{
    private LocationRepositoryInterface $locationRepository;
    /**
     * @var LocationEntity[]
     */
    private array $locationList;

    public function setLocationRepository(
        LocationRepositoryInterface $repository
    ): void {
        $this->locationRepository = $repository;
    }

    public function execute(): void
    {
        $this->checkUserIsAuthorised();
        $this->searchForLocations();
        $this->generateResponse();
        $this->sendResponse();
    }

    private function searchForLocations(): void
    {
        $this->locationList = $this->locationRepository->fuzzySearchLocation(
            $this->request->getSearchTerm()
        );
    }

    private function generateResponse(): void
    {
        $responseData = [];

        foreach ($this->locationList as $locationEntity) {
            $responseData[] = [
                LocationFuzzySearchResponse::DATA_ID =>
                    $locationEntity->getId(),
                LocationFuzzySearchResponse::DATA_NAME =>
                    $locationEntity->getLocation(),
            ];
        }

        $this->response = new LocationFuzzySearchResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::DATA => $responseData,
            ]
        );
    }

}
