<?php

namespace AppBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class HotelRepository
 *
 * @package AppBundle\Repository
 */
class HotelRepository extends EntityRepository
{

    /**
     * Возвращает запрос на получение отелей без комнат
     * с учетом поискового запроса
     *
     * @param string $alias
     * @param string $searchQuery
     *
     * @return QueryBuilder
     */
    public function getHotelsWithoutRoomsQuery(string $alias = 'h', string $searchQuery = null): QueryBuilder
    {
        $query = $this->createQueryBuilder($alias);
        $query
            ->leftJoin("$alias.rooms", 'r')
            ->groupBy("$alias.id")
            ->having('COUNT(r.id) = 0');

        if ($searchQuery) {
            $this->applySearchQuery($query, $searchQuery);
        }

        return $query;
    }

    /**
     * Возвращает запрос на получение отелей без удобств
     * с учетом поискового запроса
     *
     * @param string $alias
     * @param string $searchQuery
     *
     * @return QueryBuilder
     */
    public function getHotelsWithoutAmenitiesQuery(string $alias = 'h', string $searchQuery = null): QueryBuilder
    {
        $query = $this->createQueryBuilder($alias);
        $query
            ->leftJoin("$alias.hotelAmenities", 'a')
            ->groupBy("$alias.id")
            ->having('COUNT(a.id) = 0');

        if ($searchQuery) {
            $this->applySearchQuery($query, $searchQuery);
        }

        return $query;
    }

    /**
     * Возвращает запрос на получение отелей без фото
     * с учетом поискового запроса
     *
     * @param string $alias
     * @param string $searchQuery
     *
     * @return QueryBuilder
     */
    public function getHotelsWithoutPhotosQuery(string $alias = 'h', string $searchQuery = null): QueryBuilder
    {
        $query = $this->createQueryBuilder($alias);
        $query
            ->leftJoin("$alias.hotelPhotos", 'p')
            ->groupBy("$alias.id")
            ->having('COUNT(p.id) = 0');

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

    /**
     * метод возвращает активные отели в виде массива [id, title]
     * @return array
     */
    public function getActiveHotels($search = null): array
    {

        /** @var EntityManager $em */
        $em = $this->getEntityManager();

        //находим все отели с включенными номерными удобствами
        $sql = "select hotel.id from hotel
          inner join room on hotel.id = room.hotel_id and room.is_active = 1
          inner join deal_offer_price_room on room.id = deal_offer_price_room.room_id
          inner join room_amenities ra on room.id = ra.room_id and ra.is_active = 1
          inner join amenity a on ra.amenity_id = a.id and a.is_active = 1";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $hotelIdsWithRooms = array_column($result, 'id');

        //находим все отели с включенными отельными удобствами
        $sql = "select hotel.id from hotel
            inner join hotel_amenities ha on hotel.id = ha.hotel_id and ha.is_active=1
            inner join amenity a on ha.amenity_id = a.id and a.is_active = 1";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $hotelIdsWithAmenities = array_column($result, 'id');

        //находим все отели с активными фотками
        $sql = "select hotel.id from hotel inner join hotel_photos hp on hotel.id = hp.hotel_id and hp.is_active=1
              inner join photo p on hp.photo_id = p.id and p.is_active=1 and p.photo is not null and p.photo <> ''";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $hotelIdsWithPhotos = array_column($result, 'id');

        //находим все отели с активной акцией и ценой
        $sql = "select hotel.id from hotel
            inner join deal_offer do on hotel.id = do.hotel_id and do.is_active = 1 and do.valid_at > now()
            inner join deal_offer_price dop on do.id = dop.deal_offer_id and
                       ((dop.valid_date > now() or dop.valid_date is null) or
                       (dop.group_price_valid_date > now() or dop.group_price_valid_date is null))
            inner join deal_offer_price_room dopr on dop.id = dopr.deal_offer_price_id";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $hotelIdsWithDO = array_column($result, 'id');

        //делаем пересечение и оставляем уникальные id отелей
        $activeHotelsIds = (array_unique(array_intersect($hotelIdsWithRooms, $hotelIdsWithAmenities,
            $hotelIdsWithPhotos, $hotelIdsWithDO)));

        $query = $this->createQueryBuilder('h');
        $query->select('h.id, h.title, h.sysname');
        $query->where($query->expr()->in('h.id', $activeHotelsIds));
        $query->andWhere('h.isActive = :isActive');
        $query->andWhere('h.isProduction = :isProduction');
        $query->andWhere($query->expr()->andX(
            $query->expr()->isNotNull('h.title'),
            $query->expr()->isNotNull('h.sysname'),
            $query->expr()->neq('h.title', "''"),
            $query->expr()->neq('h.sysname', "''")
        ));
        $query->setParameter('isActive', 1)->setParameter('isProduction', 1);

        if ($search) {
            if (ctype_digit($search)) {
                $query->andWhere($query->expr()->eq('h.id', $search));
            } else {
                $query->andWhere($query->expr()->like('h.title', $query->expr()->literal('%' . $search . '%')));
            }
        }

        return $query->getQuery()->getArrayResult();
    }

     /**
     * Возвращает запрос на получение всех administrative_area отсортированных по алфавиту
     *
     * @return QueryBuilder
     */
    public function getAdministrativeAreasQuery(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('h');
        $queryBuilder
            ->select('h.administrativeArea')
            ->groupBy('h.administrativeArea')
            ->where($queryBuilder->expr()->isNotNull('h.administrativeArea'))
            ->andWhere($queryBuilder->expr()->neq('h.administrativeArea', ':emptyStr'))
            ->orderBy('h.administrativeArea')
            ->setParameter('emptyStr', '');

        return $queryBuilder;
    }
}
