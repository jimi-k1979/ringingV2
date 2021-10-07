<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\location\createLocation;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\managers\TransactionManagerInterface;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;
use Exception;

/**
 * Class CreateLocation
 * @package DrlArchive\core\interactors\location\createLocation
 * @property CreateLocationRequest $request
 */
class CreateLocation extends Interactor
{
    private DeaneryRepositoryInterface $deaneryRepository;
    private LocationRepositoryInterface $locationRepository;
    private TransactionManagerInterface $transactionManager;
    private LocationEntity $locationEntity;

    /**
     * @param DeaneryRepositoryInterface $deaneryRepository
     */
    public function setDeaneryRepository(
        DeaneryRepositoryInterface $deaneryRepository
    ): void {
        $this->deaneryRepository = $deaneryRepository;
    }

    /**
     * @param LocationRepositoryInterface $locationRepository
     */
    public function setLocationRepository(
        LocationRepositoryInterface $locationRepository
    ): void {
        $this->locationRepository = $locationRepository;
    }

    /**
     * @param TransactionManagerInterface $transactionManager
     */
    public function setTransactionManager(
        TransactionManagerInterface $transactionManager
    ): void {
        $this->transactionManager = $transactionManager;
    }

    public function execute(): void
    {
        $this->checkUserIsAuthorised(
            SecurityRepositoryInterface::ADD_NEW_PERMISSION
        );

        $this->transactionManager->startTransaction();
        try {
            $this->createEntity();
            $this->writeToDatabase();
            $this->createResponse();
            $this->transactionManager->commitTransaction();
        } catch (Exception $e) {
            $this->createFailingResponse($e);
            $this->transactionManager->rollbackTransaction();
        }

        $this->presenter->send($this->response);
    }

    private function createEntity(): void
    {
        $this->locationEntity = new LocationEntity();
        $this->locationEntity->setLocation(
            $this->request->getLocation()
        );
        $this->locationEntity->setDeanery(
            $this->deaneryRepository->selectDeanery(
                $this->request->getDeanery()
            )
        );
        $this->locationEntity->setDedication(
            $this->request->getDedication()
        );
        $this->locationEntity->setTenorWeight(
            $this->request->getTenorWeight()
        );
    }

    private function writeToDatabase(): void
    {
        $this->locationRepository->insertLocation(
            $this->locationEntity
        );
    }

    private function createResponse()
    {
        $data = [
            CreateLocationResponse::DATA_ID =>
                $this->locationEntity->getId(),
            CreateLocationResponse::DATA_LOCATION =>
                $this->locationEntity->getLocation(),
            CreateLocationResponse::DATA_DEANERY =>
                $this->locationEntity->getDeanery()->getName(),
            CreateLocationResponse::DATA_DEDICATION =>
                $this->locationEntity->getDedication(),
            CreateLocationResponse::DATA_TENOR_WEIGHT =>
                $this->locationEntity->getTenorWeight(),
        ];
        $this->response = new CreateLocationResponse([
                                                         Response::STATUS => Response::STATUS_SUCCESS,
                                                         Response::MESSAGE => 'Location successfully created',
                                                         Response::DATA => $data,
                                                     ]);
    }

    private function createFailingResponse(Exception $e)
    {
        $data = [
            Response::DATA_MESSAGE => $e->getMessage(),
            Response::DATA_CODE => $e->getCode(),
        ];
        $this->response = new CreateLocationResponse([
                                                         Response::STATUS => Response::STATUS_NOT_CREATED,
                                                         Response::MESSAGE => 'Location not created',
                                                         Response::DATA => $data,
                                                     ]);
    }

}
