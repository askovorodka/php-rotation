<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Quota;
use AppBundle\Entity\Room;
use Doctrine\DBAL\LockMode;

/**
 * Class QuotaRepository
 *
 * @package AppBundle\Repository
 */
class QuotaRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param Room               $room
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     * @param bool               $forUpdate
     * @return Quota[]
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getQuotasForRoomByInterval(
        Room $room,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        bool $forUpdate = false
    ): array {
        $qb = $this->createQueryBuilder('q')
            ->andWhere('q.room = :room')
            ->andWhere('q.date >= :startDate')
            ->andWhere('q.date <= :endDate')
            ->setParameter('room', $room)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        if ($forUpdate) {
            $qb->getQuery()->setLockMode(LockMode::PESSIMISTIC_WRITE);
        }

        return $qb->getQuery()->getResult();
    }
}
