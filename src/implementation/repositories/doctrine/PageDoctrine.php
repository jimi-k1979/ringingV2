<?php

namespace DrlArchive\implementation\repositories\doctrine;

use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\interfaces\repositories\PagesRepositoryInterface;
use DrlArchive\core\interfaces\repositories\Repository;

class PageDoctrine extends DoctrineRepository implements
    PagesRepositoryInterface
{

    public function fetchRecordsPageList(): array
    {
        try {
            $query = $this->database->createQueryBuilder();
            $query->select(
                'rr.id AS ' . Repository::ALIAS_RECORD_ID,
                'rr.recordName AS ' . Repository::ALIAS_RECORD_NAME,
                'rr.recordCategory AS ' . Repository::ALIAS_RECORD_CATEGORY
            )
                ->from('ringing_record', 'rr')
                ->where(
                    $query->expr()->eq('rr.showOnIndexPage', 1)
                )
                ->orderBy('rr.recordCategory', self::ORDER_BY_ASC);
            return $query->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'unable to fetch records list'
            );
        }
    }
}
