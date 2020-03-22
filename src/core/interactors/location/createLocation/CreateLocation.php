<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\location\createLocation;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;
use DrlArchive\core\interfaces\repositories\TransactionManagerInterface;
use Exception;

class CreateLocation extends Interactor
{

    /**
     * @var DeaneryRepositoryInterface
     */
    private $deaneryRepository;
    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;
    /**
     * @var TransactionManagerInterface
     */
    private $transactionManager;
    /**
     * @var LocationEntity
     */
    private $locationEntity;

    /**
     * @param DeaneryRepositoryInterface $deaneryRepository
     */
    public function setDeaneryRepository(DeaneryRepositoryInterface $deaneryRepository): void
    {
        $this->deaneryRepository = $deaneryRepository;
    }

    /**
     * @param LocationRepositoryInterface $locationRepository
     */
    public function setLocationRepository(LocationRepositoryInterface $locationRepository): void
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * @param TransactionManagerInterface $transactionManager
     */
    public function setTransactionManager(TransactionManagerInterface $transactionManager): void
    {
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
        $this->locationEntity = $this->locationRepository->insertLocation(
            $this->locationEntity
        );
    }

    private function createResponse()
    {
        $data = [
            'id' => $this->locationEntity->getId(),
            'location' => $this->locationEntity->getLocation(),
            'deanery' => $this->locationEntity->getDeanery()->getName(),
            'dedication' => $this->locationEntity->getDedication(),
            'tenorWeight' => $this->locationEntity->getTenorWeight(),
        ];
        $this->response = new CreateLocationResponse([
            Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
            Response::RESPONSE_MESSAGE => 'Location successfully created',
            Response::RESPONSE_DATA => $data,
        ]);
    }

    private function createFailingResponse(Exception $e)
    {
        $data = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
        ];
        $this->response = new CreateLocationResponse([
            Response::RESPONSE_STATUS => Response::STATUS_NOT_CREATED,
            Response::RESPONSE_MESSAGE => 'Location not created',
            Response::RESPONSE_DATA => $data,
        ]);
    }

}