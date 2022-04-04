<?php

use DrlArchive\core\classes\Response;
use DrlArchive\core\Exceptions\BadDataException;
use DrlArchive\core\interactors\pages\recordsPage\RecordsPageResponse;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\pages\RecordsPageFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;

require_once __DIR__ . '/../init.php';

$presenter = new class extends AbstractTwigPagePresenter {
    private const CATEGORY_COMPETITION = 'competition';
    private const CATEGORY_EVENT = 'event';
    private const CATEGORY_RINGER = 'ringer';
    private const CATEGORY_SEASONAL = 'seasonal';
    private const CATEGORY_TEAM = 'team';
    private const ID = 'id';
    private const NAME = 'name';

    private $data;

    public function send(?Response $response = null): void
    {
        parent::send($response);
        $this->dataForTemplate[self::NAV][self::NAV_HIGHLIGHTED] =
            Implementation::NAV_HIGHLIGHT_STATS;

        if ($response->getStatus() === Response::STATUS_SUCCESS) {
            try {
                $this->data = $response->getData();
                $this->categoriseData();

                $this->twig->display(
                    'statistics/records.twig',
                    $this->dataForTemplate
                );
            } catch (Throwable $e) {
                echo $response->getMessage();
            }
        }
    }

    private function categoriseData(): void
    {
        $seasonalRecords = [];
        $competitionRecords = [];
        $eventRecords = [];
        $teamRecords = [];
        $ringerRecords = [];

        foreach ($this->data as $record) {
            switch ($record[RecordsPageResponse::DATA_RECORD_CATEGORY]) {
                case self::CATEGORY_COMPETITION:
                    $competitionRecords[] = [
                        self::NAME =>
                            $record[RecordsPageResponse::DATA_RECORD_NAME],
                        self::ID =>
                            $record[RecordsPageResponse::DATA_RECORD_ID],
                    ];
                    break;

                case self::CATEGORY_EVENT:
                    $eventRecords[] = [
                        self::NAME =>
                            $record[RecordsPageResponse::DATA_RECORD_NAME],
                        self::ID =>
                            $record[RecordsPageResponse::DATA_RECORD_ID],
                    ];
                    break;

                case self::CATEGORY_RINGER:
                    $ringerRecords[] = [
                        self::NAME =>
                            $record[RecordsPageResponse::DATA_RECORD_NAME],
                        self::ID =>
                            $record[RecordsPageResponse::DATA_RECORD_ID],
                    ];
                    break;

                case self::CATEGORY_SEASONAL:
                    $seasonalRecords[] = [
                        self::NAME =>
                            $record[RecordsPageResponse::DATA_RECORD_NAME],
                        self::ID =>
                            $record[RecordsPageResponse::DATA_RECORD_ID],
                    ];
                    break;

                case self::CATEGORY_TEAM:
                    $teamRecords[] = [
                        self::NAME =>
                            $record[RecordsPageResponse::DATA_RECORD_NAME],
                        self::ID =>
                            $record[RecordsPageResponse::DATA_RECORD_ID],
                    ];
                    break;

                default:
                    throw new BadDataException(
                        'Unknown category'
                    );
            }
            $this->dataForTemplate[self::CATEGORY_COMPETITION] =
                $competitionRecords;
            $this->dataForTemplate[self::CATEGORY_EVENT] = $eventRecords;
            $this->dataForTemplate[self::CATEGORY_RINGER] = $ringerRecords;
            $this->dataForTemplate[self::CATEGORY_SEASONAL] = $seasonalRecords;
            $this->dataForTemplate[self::CATEGORY_TEAM] = $teamRecords;
        }
    }


};

$useCase = (new RecordsPageFactory())->create(
    $presenter
);

$useCase->execute();
