<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AmenityCategory;
use AppBundle\Entity\Hotel;
use AppBundle\Entity\Room;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * Class AmenityRepository
 * @package AppBundle\Repository
 */
class AmenityRepository extends EntityRepository
{
    const IS_ACTIVE = 1;
    const IS_PRODUCTION = 1;

    /**
     * метод возвращает активные отельные удобства по id категории удобства и id отелей
     * @param AmenityCategory $amenityCategory
     * @param array $hotelsIds
     * @return array|mixed
     */
    public function getActiveByCategoryAndHotelsIds(AmenityCategory $amenityCategory, array $hotelsIds)
    {
        $query = $this->createQueryBuilder('amenity');
        $query
            ->distinct()
            ->innerJoin('amenity.hotelAmenities', 'ha')
            ->innerJoin('ha.hotel', 'hotel')
            ->where('amenity.isActive =:is_active')
            ->andWhere('ha.isActive =:is_active')
            ->andWhere('hotel.isActive =:is_active')
            ->andWhere('hotel.isProduction =:is_production')
            ->andWhere('amenity.amenityCategory =:amenity_category')
            ->andWhere('hotel.id in (:hotelsIds)')
            ->setParameter('is_active', self::IS_ACTIVE)
            ->setParameter('is_production', self::IS_PRODUCTION)
            ->setParameter('amenity_category', $amenityCategory)
            ->setParameter('hotelsIds', $hotelsIds);

        return $query->getQuery()->getResult();

    }

    /**
     * метод возвращает активные удобства по id категории и id активных dealOffers
     * @param AmenityCategory $amenityCategory
     * @param array $activeDealOffersIds
     * @return array|mixed
     */
    public function getActiveByCategoryAndDealOffers(AmenityCategory $amenityCategory, array $activeDealOffersIds)
    {
        $query = $this->createQueryBuilder('amenity');
        $query
            ->distinct()
            ->innerJoin('amenity.roomAmenities', 'ra')
            ->innerJoin('ra.room', 'room')
            ->innerJoin('room.dealOfferRoomPrices', 'dopr')
            ->innerJoin('dopr.dealOfferPrice', 'dop')
            ->where('amenity.isActive =:is_active')
            ->andWhere('amenity.amenityCategory =:amenity_category')
            ->andWhere('ra.isActive =:is_active')
            ->andWhere('room.isActive =:is_active')
            ->andWhere('dop.dealOffer in (:dealOffersIds)')
            ->setParameter('is_active', self::IS_ACTIVE)
            ->setParameter('amenity_category', $amenityCategory)
            ->setParameter('dealOffersIds', $activeDealOffersIds);

        return $query->getQuery()->getResult();

    }

    /**
     * Возвращает запрос на получение всех активных удобств
     * и связь указанного отеля с удобством - @see \AppBundle\Entity\HotelAmenity
     *
     * @param Hotel  $hotel
     * @param string $amenityAlias
     * @param string $hotelAmenityAlias
     * @param string $amenityCategoryAlias
     *
     * @return QueryBuilder
     */
    public function getActiveAmenitiesWithHotelAmenitiesQuery(
        Hotel $hotel,
        $amenityAlias = 'a',
        $hotelAmenityAlias = 'ha',
        $amenityCategoryAlias = 'ac'
    ): QueryBuilder {
        $query = $this->createQueryBuilder($amenityAlias);
        $query
            ->addSelect($hotelAmenityAlias)
            ->andWhere("$amenityAlias.isActive = 1")
            ->join(
                "$amenityAlias.amenityCategory",
                $amenityCategoryAlias,
                Join::WITH,
                "$amenityCategoryAlias.systemName = :hotelAmenityCategorySystemName"
            )
            ->leftJoin(
                "$amenityAlias.hotelAmenities",
                $hotelAmenityAlias,
                Join::WITH,
                "$hotelAmenityAlias.hotel = :hotel"
            )
            ->setParameter('hotel', $hotel)
            ->setParameter('hotelAmenityCategorySystemName', AmenityCategory::HOTEL_AMENITIES_CATEGORY_SYSTEM_NAME);

        return $query;
    }

    /**
     * Возвращает запрос на получение всех активных удобств
     * и связь указанной комнаты с удобством - @see \AppBundle\Entity\RoomAmenity
     *
     * @param Room   $room
     * @param string $amenityAlias
     * @param string $roomAmenityAlias
     * @param string $amenityCategoryAlias
     *
     * @return QueryBuilder
     */
    public function getActiveAmenitiesWithRoomAmenitiesQuery(
        Room $room,
        $amenityAlias = 'a',
        $roomAmenityAlias = 'ra',
        $amenityCategoryAlias = 'ac'
    ): QueryBuilder {
        $query = $this->createQueryBuilder($amenityAlias);
        $query
            ->addSelect($roomAmenityAlias)
            ->andWhere("$amenityAlias.isActive = 1")
            ->join(
                "$amenityAlias.amenityCategory",
                $amenityCategoryAlias,
                Join::WITH,
                "$amenityCategoryAlias.systemName = :roomAmenityCategorySystemName"
            )
            ->leftJoin(
                "$amenityAlias.roomAmenities",
                $roomAmenityAlias,
                Join::WITH,
                "$roomAmenityAlias.room = :room"
            )
            ->setParameter('room', $room)
            ->setParameter('roomAmenityCategorySystemName', AmenityCategory::ROOM_AMENITIES_CATEGORY_SYSTEM_NAME);

        return $query;
    }
}
