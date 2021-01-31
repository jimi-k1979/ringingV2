<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\location\fetchLocationByName;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use Throwable;

/**
 * Class FetchLocationByName
 * @package DrlArchive\core\interactors\location\fetchLocationByName
 * @property FetchLocationByNameRequest $request
 */
class FetchLocationByName extends Interactor
{

    /**
     * @var LocationRepositoryInterface
     */
    private LocationRepositoryInterface $locationRepository;
    private LocationEntity $location;

    public function setLocationRepository(LocationRepositoryInterface $repository)
    {
        $this->locationRepository = $repository;
    }

    public function execute(): void
    {
        try {
            $this->checkUserIsAuthorised();
            $this->fetchData();
            $this->createResponse();
        } catch (Throwable $e) {
            $this->createFailureResponse($e);
        }

        $this->sendResponse();
    }

    /**
     * @throws CleanArchitectureException
     */
    private function fetchData(): void
    {
        $this->location = $this->locationRepository->fetchLocationByName(
            $this->request->getName()
        );
    }

    private function createResponse(): void
    {
        $this->response = new FetchLocationByNameResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setData(
            [
                'id' => $this->location->getId(),
                'name' => $this->location->getLocation(),
                'dedication' => $this->location->getDedication(),
                'tenorWeight' => $this->location->getTenorWeight(),
                'numberOfBells' => $this->location->getNumberOfBells(),
                'deanery' => $this->location->getDeanery()->getName(),
                'region' => $this->location->getDeanery()->getRegion(),
            ]
        );
    }

    private function createFailureResponse(Throwable $e): void
    {
        $this->response = new FetchLocationByNameResponse();
        $this->response->setStatus(Response::STATUS_NOT_FOUND);
        $this->response->setMessage("{$e->getCode()}: {$e->getMessage()}");
    }


}
