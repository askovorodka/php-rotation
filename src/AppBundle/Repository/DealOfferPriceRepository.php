<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class DealOfferPriceRepository
 *
 * @package AppBundle\Repository
 */
class DealOfferPriceRepository extends EntityRepository
{
    /**
     * Возвращает запрос на получение цен без комнат
     * с учетом поискового запроса
     *
     * @param string      $alias
     * @param string|null $searchQuery
     *
     * @return QueryBuilder
     */
    public function getDealOfferPricesWithoutRoomsQuery(string $alias = 'dop', string $searchQuery = null): QueryBuilder
    {
        $query = $this->createQueryBuilder($alias);

        $query
            ->leftJoin("$alias.dealOfferRooms", 'r')
            ->innerJoin("$alias.dealOffer", 'do')
            ->andWhere('do.hotel is not null')
            ->andWhere("$alias.validDate > :now")
            ->groupBy("$alias.id")
            ->having('COUNT(r.id) = 0')
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
                $query->expr()->eq("$alias.id", ':searchInt'),
                $query->expr()->eq("$alias.originalPrice", ':searchInt'),
                $query->expr()->eq("$alias.discount", ':searchInt')
            );
            $query->andWhere($whereExpr)->setParameter('searchInt', (int)$searchQuery);
        } else {
            $whereExpr = $query->expr()->orX(
                $query->expr()->like("$alias.title", ':searchString')
            );
            $query
                ->andWhere($whereExpr)
                ->setParameter('searchString', '%' . addcslashes($searchQuery, '%_') . '%');
        }
    }
}
