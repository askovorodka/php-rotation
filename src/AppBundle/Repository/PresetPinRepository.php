<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Preset;
use AppBundle\Entity\PresetPin;
use Doctrine\ORM\EntityRepository;

/**
 * Class PresetPinRepository
 *
 * @package AppBundle\Repository
 */
class PresetPinRepository extends EntityRepository
{
    /**
     * @param int|null $presetId
     * @param int|null $cityId
     * @param int|null $regionId
     * @return PresetPin[]
     * @throws \Doctrine\ORM\ORMException
     */
    public function getActivePins(
        int $presetId = null,
        int $cityId = null,
        int $regionId = null
    ): array {
        $qb = $this->createQueryBuilder('pp');
        $qb->andWhere('pp.isActive = 1');

        if ($presetId) {
            $presetRef = $this->getEntityManager()->getReference(Preset::class, $presetId);

            $qb->andWhere($qb->expr()->orX(
                'pp.basePreset is NULL',
                'pp.basePreset = :preset'
            ))->setParameter('preset', $presetRef);
        }

        if ($cityId) {
            $qb->andWhere($qb->expr()->orX(
                'pp.cityId is NULL',
                'pp.cityId = :cityId'
            ))->setParameter('cityId', $cityId);
        }

        if ($regionId) {
            $qb->andWhere($qb->expr()->orX(
                'pp.regionId is NULL',
                'pp.regionId = :regionId'
            ))->setParameter('regionId', $regionId);
        }

        return $qb->getQuery()->getResult();
    }

}
