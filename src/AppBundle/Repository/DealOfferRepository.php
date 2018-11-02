<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class DealOfferRepository
 *
 * @package AppBundle\Repository
 */
class DealOfferRepository extends EntityRepository
{
    /**
     * Возвращает запрос на получение акций без отелей
     * с учетом поискового запроса
     *
     * @param string      $alias
     * @param string|null $searchQuery
     *
     * @return QueryBuilder
     */
    public function getDealOffersWithoutHotelQuery(string $alias = 'do', string $searchQuery = null): QueryBuilder
    {
        $query = $this->createQueryBuilder($alias);
        $query
            ->where($query->expr()->isNull("$alias.hotel"))
            ->andWhere("$alias.isActive = true")
            ->andWhere("$alias.endAt >= :now")
            ->setParameter('now', new \DateTime());

        if ($searchQuery) {
            $this->applySearchQuery($query, $searchQuery);
        }

        return $query;
    }

    /**
     * метод возвращает активные [deal_offer_id, titleLink, sysname, hotelId] у которых есть отели
     *
     * @return array|mixed
     */
    public function getDealOfferIdsWithHotels()
    {
        $query = $this->createQueryBuilder('do');
        $query
            ->select('do.id, do.titleLink', 'h.sysname', 'h.id as hotelId')
            ->innerJoin('do.dealOfferPrices', 'dop')
            ->innerJoin('dop.dealOfferRooms', 'dor')
            ->innerJoin('do.hotel', 'h')
            ->where('do.isActive = :is_active')
            ->andWhere('do.validAt >= :now')
            ->andWhere('dop.validDate >= :now')
            ->andWhere('dop.groupPriceValidDate >= :now')
            ->andWhere('h.isActive = :is_active')
            ->andWhere('h.isProduction = :is_production')
            ->distinct()
            ->setParameter('now', date_create()->format('Y-m-d H:i:s'))
            ->setParameter('is_active', 1)
            ->setParameter('is_production', 1);

        return $query->getQuery()->getResult();
    }

    /**
     * Возвращает запрос на получение активных акций связанных с отелями
     * с учетом поискового запроса
     *
     * @param string      $alias
     * @param string|null $searchQuery
     *
     * @return QueryBuilder
     */
    public function getActiveDealOffersWithHotelsQuery(string $alias = 'do', string $searchQuery = null): QueryBuilder
    {
        $query = $this->createQueryBuilder($alias);
        $query
            ->andWhere("$alias.isActive = 1")
            ->andWhere("$alias.validAt > :now")
            ->andWhere($query->expr()->isNotNull("$alias.hotel"))
            ->innerJoin("$alias.dealOfferPrices",'dop')
            ->andWhere('dop.validDate > :now')
            ->setParameter('now', new \DateTime());

        if ($searchQuery) {
            $this->applySearchQuery($query, $searchQuery);
        }

        return $query;
    }

    /**
     * Добавляет в запрос условия по переданной строке поиска
     *
     * @param QueryBuilder $query
     * @param string       $searchQuery
     */
    public function applySearchQuery(QueryBuilder $query, string $searchQuery): void
    {
        $alias = $query->getRootAliases()[0];

        if (is_numeric($searchQuery)) {
            $whereExpr = $query->expr()->orX(
                $query->expr()->eq("$alias.id", ':searchInt')
            );
            $query->andWhere($whereExpr)->setParameter('searchInt', (int)$searchQuery);
        } else {
            $whereExpr = $query->expr()->orX(
                $query->expr()->like("$alias.title", ':searchString'),
                $query->expr()->like("$alias.address", ':searchString')
            );
            $query
                ->andWhere($whereExpr)
                ->setParameter('searchString', '%' . addcslashes($searchQuery, '%_') . '%');
        }
    }

}
