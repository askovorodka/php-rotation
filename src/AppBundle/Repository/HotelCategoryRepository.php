<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class HotelCategoryRepository extends EntityRepository
{
    const IS_ACTIVE = 1;

    /**
     * метод находит активные категории отелей по id активных отелей
     * @param array $hotelsIds
     * @return array|mixed
     */
    public function getActiveByHotelsIds(array $hotelsIds) {
        $query = $this->createQueryBuilder('hcategory');
        $query
            ->distinct()
            ->innerJoin('hcategory.hotels', 'hotel')
            ->where('hcategory.isActive =:is_active')
            ->andWhere('hotel.id in (:hotels_ids)')
            ->setParameter('is_active', self::IS_ACTIVE)
            ->setParameter('hotels_ids', $hotelsIds);

        return $query->getQuery()->getResult();
    }
}
