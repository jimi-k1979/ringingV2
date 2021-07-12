<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\interfaces\repositories\RingerRepositoryInterface;

class RingerDoctrine extends DoctrineRepository implements
    RingerRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function fuzzySearchRinger(string $searchTerm): array
    {
        // TODO: Implement fuzzySearchRinger() method.
    }

    /**
     * @inheritDoc
     */
    public function fetchWinningTeamByEvent(DrlEventEntity $event): array
    {
        $query = $this->database->createQueryBuilder();

        $query->select(
            [

            ]
        )
    }
}
