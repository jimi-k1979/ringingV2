<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\location\locationFuzzySearch;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;

class LocationFuzzySearch extends Interactor
{

    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;
    /**
     * @var LocationEntity[]
     */
    private $locationList;

    public function setLocationRepository(
        LocationRepositoryInterface $repository
    ): void {
        $this->locationRepository = $repository;
    }

    public function execute(): void
    {
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
                'id' => $locationEntity->getId(),
                'location' => $locationEntity->getLocation()
            ];
        }

        $this->response = new LocationFuzzySearchResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => $responseData,
            ]
        );
    }

}