<?php

namespace AppBundle\Repository;

use AppBundle\Entity\LocalHotelPin;
use AppBundle\Entity\Preset;
use Doctrine\ORM\EntityRepository;

/**
 * Class LocalHotelPinRepository
 *
 * @package AppBundle\Repository
 */
class LocalHotelPinRepository extends EntityRepository
{
    /**
     * @param int $presetId
     * @param int $cityId
     * @param int $regionId
     * @return LocalHotelPin[]
     * @throws \Doctrine\ORM\ORMException
     */
    public function getActivePins(int $presetId, int $cityId = null, int $regionId = null): array
    {
        $qb = $this->createQueryBuilder('lhp');
        $qb->andWhere('lhp.isActive = 1');

        $presetRef = $this->getEntityManager()->getReference(Preset::class, $presetId);
        $qb->andWhere('lhp.preset = :preset')
            ->setParameter('preset', $presetRef);

        if ($cityId) {
            $qb->andWhere($qb->expr()->orX(
                'lhp.cityId is NULL',
                'lhp.cityId = :cityId'
            ))->setParameter('cityId', $cityId);
        }

        if ($regionId) {
            $qb->andWhere($qb->expr()->orX(
                'lhp.regionId is NULL',
                'lhp.regionId = :regionId'
            ))->setParameter('regionId', $regionId);
        }

        return $qb->getQuery()->getResult();
    }

}
