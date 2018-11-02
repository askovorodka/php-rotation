<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Preset;
use Doctrine\ORM\EntityRepository;

/**
 * Class PresetRepository
 *
 * @package AppBundle\Repository
 */
class PresetRepository extends EntityRepository
{
    /**
     * @param string $presetCategorySysname
     * @param int    $cityId
     * @param int    $regionId
     * @return Preset|null
     */
    public function getActivePreset(
        string $presetCategorySysname,
        ?int $cityId,
        ?int $regionId
    ): ?Preset {
        $qb = $this->createQueryBuilder('p');

        $qb->andWhere('p.isActive = 1');

        $qb->innerJoin('p.presetCategory', 'pc')
            ->andWhere('pc.sysname = :presetCategorySysname')
            ->setParameter('presetCategorySysname', $presetCategorySysname);

        $qb->andWhere($qb->expr()->orX(
            'p.cityId = :cityId',
            'p.regionId = :regionId'
        ))->setParameter('cityId', $cityId)
            ->setParameter('regionId', $regionId);

        $qb->addOrderBy('p.cityId', 'desc')
            ->addOrderBy('p.regionId', 'desc');

        $qb->setMaxResults(1);

        $result = $qb->getQuery()->getResult();

        if ($result && $preset = current($result)) {
            return $preset;
        }

        return null;
    }

    /**
     * @param string $sysname
     * @return Preset|null|object
     */
    public function getActivePresetBySysname(string $sysname)
    {
        return $this->findOneBy([
            'isActive' => 1,
            'sysname' => $sysname,
        ]);
    }

}
