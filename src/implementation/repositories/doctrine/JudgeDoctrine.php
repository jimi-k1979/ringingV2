<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use Doctrine\DBAL\Query\QueryBuilder;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;
use DrlArchive\core\interfaces\repositories\Repository;
use Throwable;

class JudgeDoctrine extends DoctrineRepository implements
    JudgeRepositoryInterface
{
    private const FIELD_JUDGE_ID = 'j.id';
    private const FIELD_FIRST_NAME = 'j.firstName';
    private const FIELD_LAST_NAME = 'j.lastName';
    private const FIELD_RINGER_ID = 'j.ringerID';
    private const FIELD_DRL_EVENT_JUDGE_EVENT_ID = 'dej.eventID';

    /**
     * @inheritDoc
     */
    public function fetchJudgesByDrlEvent(DrlEventEntity $entity): array
    {
        try {
            $query = $this->database->createQueryBuilder();
            $query->select(
                [
                    self::FIELD_JUDGE_ID . ' AS ' . Repository::ALIAS_JUDGE_ID,
                    self::FIELD_FIRST_NAME . ' AS ' . Repository::ALIAS_FIRST_NAME,
                    self::FIELD_LAST_NAME . ' AS ' . Repository::ALIAS_LAST_NAME,
                    self::FIELD_RINGER_ID . ' AS ' . Repository::ALIAS_RINGER_ID,
                ]
            )
                ->from('judge', 'j')
                ->leftJoin(
                    'j',
                    'DRL_event_judge',
                    'dej',
                    'j.id = dej.judgeId'
                )
                ->where(
                    $query->expr()->eq(
                        self::FIELD_DRL_EVENT_JUDGE_EVENT_ID,
                        ':event'
                    )
                )
                ->setParameter('event', $entity->getId());
            $results = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No judges found - connection error',
                JudgeRepositoryInterface::NO_RECORDS_FOUND_EXCEPTION
            );
        }

        if (empty($results)) {
            return [];
        }
        return $this->generateJudgeEntityArray($results);
    }

    /**
     * @param array $results
     * @return JudgeEntity[]
     */
    private function generateJudgeEntityArray(array $results): array
    {
        $returnArray = [];
        foreach ($results as $row) {
            $returnArray[] = $this->generateJudgeEntity($row);
        }
        return $returnArray;
    }

    private function generateJudgeEntity(array $row): JudgeEntity
    {
        $entity = new JudgeEntity();
        $entity->setRinger(new RingerEntity());

        if (isset($row[Repository::ALIAS_JUDGE_ID])) {
            $entity->setId((int)$row[Repository::ALIAS_JUDGE_ID]);
        }
        if (isset($row[Repository::ALIAS_FIRST_NAME])) {
            $entity->setFirstName($row[Repository::ALIAS_FIRST_NAME]);
            $entity->getRinger()->setFirstName(
                $row[Repository::ALIAS_FIRST_NAME]
            );
        }
        if (isset($row[Repository::ALIAS_LAST_NAME])) {
            $entity->setLastName($row[Repository::ALIAS_LAST_NAME]);
            $entity->getRinger()->setLastName(
                $row[Repository::ALIAS_LAST_NAME]
            );
        }
        if (isset($row[Repository::ALIAS_RINGER_ID])) {
            $entity->getRinger()->setId(
                (int)$row[Repository::ALIAS_RINGER_ID]
            );
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function fetchJudgeById(int $id): JudgeEntity
    {
        $query = $this->baseJudgeAllFieldsSelect();

        $query->where(
            $query->expr()->eq('j.id', ':id')
        )
            ->setParameter('id', $id);
        $result = $query->executeQuery()->fetchAssociative();

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No judge found with that id',
                JudgeRepositoryInterface::NO_RECORDS_FOUND_EXCEPTION
            );
        }

        return $this->generateJudgeEntity($result);
    }

    private function baseJudgeAllFieldsSelect(): QueryBuilder
    {
        $query = $this->database->createQueryBuilder();

        $query->select(
            'j.id AS ' . Repository::ALIAS_JUDGE_ID,
            'j.firstName AS ' . Repository::ALIAS_FIRST_NAME,
            'j.lastName AS ' . Repository::ALIAS_LAST_NAME,
            'j.ringerID AS ' . Repository::ALIAS_RINGER_ID
        )
            ->from('judge', 'j');

        return $query;
    }
}
