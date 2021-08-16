<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use Doctrine\DBAL\Exception;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\entities\WinningRingerEntity;
use DrlArchive\core\interfaces\repositories\Repository;
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
        return [];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function fetchWinningTeamByEvent(DrlEventEntity $event): array
    {
        $query = $this->database->createQueryBuilder();

        $query->select(
            'r.id AS ' . self::ALIAS_RINGER_ID,
            'r.firstName AS ' . self::ALIAS_FIRST_NAME,
            'r.lastName AS ' . self::ALIAS_LAST_NAME,
            'dewr.bell AS ' . self::ALIAS_BELL
        )
            ->from('ringer', 'r')
            ->innerJoin(
                'r',
                'DRL_event_winning_ringer',
                'dewr',
                'r.id = dewr.ringerID'
            )
            ->where(
                $query->expr()->eq('dewr.eventID', ':eventId')
            )
            ->orderBy(self::ALIAS_BELL)
            ->setParameter('eventId', $event->getId());

        $results = $query->executeQuery()->fetchAllAssociative();

        return $this->generateWinningTeam($results);
    }

    private function generateWinningTeam(array $results): array
    {
        $team = [];
        foreach ($results as $result) {
            $winningRinger = new WinningRingerEntity();
            $ringer = new RingerEntity();

            if (isset($result[Repository::ALIAS_BELL])) {
                $winningRinger->setBell(
                    $result[Repository::ALIAS_BELL]
                );
            }
            if (isset($result[Repository::ALIAS_FIRST_NAME])) {
                $ringer->setFirstName(
                    $result[Repository::ALIAS_FIRST_NAME]
                );
            }
            if (isset($result[Repository::ALIAS_LAST_NAME])) {
                $ringer->setLastName(
                    $result[Repository::ALIAS_LAST_NAME]
                );
            }
            if (isset($result[Repository::ALIAS_RINGER_ID])) {
                $ringer->setId(
                    (int)$result[Repository::ALIAS_RINGER_ID]
                );
            }
            $winningRinger->setRinger($ringer);
            $team[] = $winningRinger;
        }

        return $team;
    }
}
