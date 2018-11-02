<?php

namespace AppBundle\Services\HotelsSearch;

use AppBundle\Dto\HotelsSearch\SearchDto;
use AppBundle\Services\HotelsSearch\Exceptions\HotelNotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class HotelsDbSearchService implements HotelsSearchServiceInterface
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var FoundHotels|null
     */
    private $searchCache;

    /**
     * HotelsDbSearchService constructor.
     *
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param SearchDto $searchDto
     * @return QueryBuilder
     */
    private function buildSearchQuery(SearchDto $searchDto): QueryBuilder
    {
        $administrativeArea = $searchDto->administrativeArea;
        $hotelCategories = $searchDto->hotelCategories;
        $priceGte = $searchDto->priceGte;
        $priceLte = $searchDto->priceLte;
        $roomAmenities = $searchDto->roomAmenities;
        $hotelAmenities = $searchDto->hotelAmenities;
        $order = $searchDto->order;

        $query = $this->conn->createQueryBuilder();
        $query->select('id, deal_offer_price_id, original_price, discount, price')
            ->from('hotel_with_min_price');

        if ($administrativeArea) {
            $query->andWhere('administrative_area = :administrative_area')
                ->setParameter('administrative_area', $administrativeArea);
        }

        if ($hotelCategories) {
            $query->andWhere('hotel_category_id in (:hotel_categories)')
                ->setParameter('hotel_categories', $hotelCategories, Connection::PARAM_INT_ARRAY);
        }

        if ($priceLte) {
            $query->andWhere('price <= :price_lte')
                ->setParameter('price_lte', $priceLte);
        }

        if ($priceGte) {
            $query->andWhere('price >= :price_gte')
                ->setParameter('price_gte', $priceGte);
        }

        if ($roomAmenities || $hotelAmenities) {
            $subQb = $this->conn->createQueryBuilder();
            $subQb->select('r.hotel_id')
                ->from('deal_offer_price_with_price', 'dopwp')
                ->innerJoin(
                    'dopwp',
                    'deal_offer_price_room',
                    'dopr',
                    'dopwp.id = dopr.deal_offer_price_id'
                )->innerJoin(
                    'dopr',
                    'room',
                    'r',
                    'dopr.room_id = r.id and r.is_active = 1'
                );

            if ($roomAmenities) {
                $subQb->innerJoin(
                    'r',
                    'room_amenities',
                    'ra',
                    'r.id = ra.room_id and ra.is_active = 1'
                )->innerJoin(
                    'ra',
                    'amenity',
                    'a_room',
                    'ra.amenity_id = a_room.id and a_room.is_active = 1'
                );

                $subQb->andWhere('a_room.id in (:room_amenities)')
                    ->setParameter('room_amenities', $roomAmenities, Connection::PARAM_INT_ARRAY)
                    ->andHaving('count(distinct a_room.id) = :room_amenities_cnt')
                    ->setParameter('room_amenities_cnt', count($roomAmenities));
            }

            if ($hotelAmenities) {
                $subQb->innerJoin(
                    'r',
                    'hotel',
                    'h',
                    'r.hotel_id = h.id and h.is_active = 1 and h.is_production = 1'
                )->innerJoin(
                    'h',
                    'hotel_amenities',
                    'ha',
                    'h.id = ha.hotel_id and ha.is_active = 1'
                )->innerJoin(
                    'ha',
                    'amenity',
                    'a_hotel',
                    'ha.amenity_id = a_hotel.id and a_hotel.is_active = 1'
                );

                $subQb->andWhere('a_hotel.id in (:hotel_amenities)')
                    ->setParameter('hotel_amenities', $hotelAmenities, Connection::PARAM_INT_ARRAY)
                    ->andHaving('count(distinct a_hotel.id) = :hotel_amenities_cnt')
                    ->setParameter('hotel_amenities_cnt', count($hotelAmenities));
            }

            $subQb->groupBy('r.hotel_id');

            $parameters = array_merge(
                $query->getParameters(),
                $subQb->getParameters()
            );

            $parameterTypes = array_merge(
                $query->getParameterTypes(),
                $subQb->getParameterTypes()
            );

            $query->andWhere('id in (' . $subQb->getSQL() . ')')
                ->setParameters($parameters, $parameterTypes);
        }

        if ($order) {
            $order = array_intersect_key($order, array_flip(self::AVAILABLE_ORDER_FIELDS));
            foreach ($order as $fieldName => $direction) {
                $query->addOrderBy(
                    //$query->createNamedParameter($fieldName), //не работает
                    $fieldName,
                    $direction == 'asc' ? 'asc' : 'desc'
                );
            }
        }

        $query->addOrderBy('purchases_count', 'desc');

        return $query;
    }

    /**
     * @param SearchDto $searchDto
     * @return string
     */
    private function getSearchKey(SearchDto $searchDto): string
    {
        return md5((string)$searchDto);
    }

    private function buildFoundHotel(array $row): FoundHotel
    {
        $minPrice = new FoundHotelMinPrice(
            $row['deal_offer_price_id'],
            $row['original_price'],
            $row['discount'],
            $row['price']
        );

        return new FoundHotel($row['id'], $minPrice);
    }

    /**
     * @inheritdoc
     */
    public function search(SearchDto $searchDto): FoundHotels
    {
        $searchKey = $this->getSearchKey($searchDto);

        if (isset($this->searchCache[$searchKey])) {
            return $this->searchCache[$searchKey];
        }

        $query = $this->buildSearchQuery($searchDto);

        $result = $query->execute()->fetchAll();

        $foundHotels = new FoundHotels();

        foreach ($result as $row) {
            $foundHotel = $this->buildFoundHotel($row);
            $foundHotels->add($foundHotel);
        }

        return $this->searchCache[$searchKey] = $foundHotels;
    }

    /**
     * @inheritdoc
     */
    public function findBySysname(string $sysname): FoundHotel
    {
        $query = $this->conn->createQueryBuilder();
        $query->select('id, deal_offer_price_id, original_price, discount, price')
            ->from('hotel_with_min_price')
            ->where('sysname = :sysname')
            ->setParameter('sysname', $sysname);

        $result = $query->execute()->fetchAll();

        if (!$result) {
            throw new HotelNotFoundException();
        }

        $row = current($result);

        return $this->buildFoundHotel($row);
    }

    /**
     * @inheritdoc
     */
    public function findById(int $id): FoundHotel
    {
        $query = $this->conn->createQueryBuilder();
        $query->select('id, deal_offer_price_id, original_price, discount, price')
            ->from('hotel_with_min_price')
            ->where('id = :id')
            ->setParameter('id', $id);

        $result = $query->execute()->fetchAll();

        if (!$result) {
            throw new HotelNotFoundException();
        }

        $row = current($result);

        return $this->buildFoundHotel($row);
    }
}
