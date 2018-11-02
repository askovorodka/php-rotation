<?php

namespace AppBundle\Repository;

use AppBundle\Dto\HotelMap\HotelMapPhotoDto;
use AppBundle\Dto\HotelMap\HotelMapPointDto;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

/**
 * Class HotelMapRepository
 *
 * @package AppBundle\Repository
 */
class HotelMapRepository
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * HotelMapRepository constructor.
     *
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Возвращает массив данных, представляющих точки на карте отелей
     *
     * @param string|null $area
     * @return HotelMapPointDto[]
     */
    public function getHotelMapPoints(string $area = null): array
    {
        $query = $this->conn->createQueryBuilder();
        $query->from('hotel_with_min_price', 'hwmp')
            ->innerJoin('hwmp', 'hotel', 'h', 'hwmp.id = h.id')
            ->innerJoin('hwmp', 'deal_offer', 'do', 'hwmp.deal_offer_id = do.id')
            ->addSelect('hwmp.deal_offer_id    as dealOfferId')
            ->addSelect('h.administrative_area as administrativeArea')
            ->addSelect('h.id                  as hotelId')
            ->addSelect('h.sysname             as sysname')
            ->addSelect('h.title               as hotelTitle')
            ->addSelect('do.title_link         as titleLink')
            ->addSelect('do.title              as dealOfferTitle')
            ->addSelect('h.rating              as rating')
            ->addSelect('h.purchases_count     as salesCount')
            ->addSelect('h.locality            as locality')
            ->addSelect('hwmp.price            as minPrice')
            ->addSelect('hwmp.original_price   as minOriginalPrice')
            ->addSelect('hwmp.discount         as discount')
            ->addSelect('h.latitude            as latitude')
            ->addSelect('h.longitude           as longitude')
            ->andWhere(
                $query->expr()->andX(
                    'h.latitude is not null',
                    'h.longitude is not null'
                )
            );

        if ($area) {
            $query->andWhere('h.administrative_area = :area')
                ->setParameter('area', $area);
        }

        $queryResult = $query->execute()->fetchAll();

        $mapPoints = [];

        foreach ($queryResult as $row) {
            $mapPointDto = new HotelMapPointDto();
            $mapPointDto->dealOfferId = (int)$row['dealOfferId'];
            $mapPointDto->administrativeArea = $row['administrativeArea'];
            $mapPointDto->hotelId = (int)$row['hotelId'];
            $mapPointDto->sysname = $row['sysname'];
            $mapPointDto->hotelTitle = $row['hotelTitle'];
            $mapPointDto->titleLink = $row['titleLink'];
            $mapPointDto->dealOfferTitle = $row['dealOfferTitle'];
            $mapPointDto->rating = $row['rating'] === null ? null : (float)$row['rating'];
            $mapPointDto->salesCount = (int)$row['salesCount'];
            $mapPointDto->locality = $row['locality'];
            $mapPointDto->minPrice = (int)$row['minPrice'];
            $mapPointDto->minOriginalPrice = (int)$row['minOriginalPrice'];
            $mapPointDto->discount = (int)$row['discount'];
            $mapPointDto->latitude = $row['latitude'];
            $mapPointDto->longitude = $row['longitude'];

            $mapPoints[$mapPointDto->hotelId] = $mapPointDto;
        }

        return $mapPoints;
    }

    /**
     * Возвращает массив данных, с помощью которых определяются отображаемые фотографии на карте отелей
     *
     * @param int[] $hotelsIds
     * @return HotelMapPhotoDto[]
     */
    public function getHotelMapPhotos(array $hotelsIds): array
    {
        if (!$hotelsIds) {
            return [];
        }

        try {
            $stmt = $this->conn->prepare('
                select hp.hotel_id    as hotelId,
                       hp.id          as photoId,
                       hp.area_width  as areaWidth,
                       hp.area_height as areaHeight,
                       hp.offset_top  as offsetTop,
                       hp.offset_left as offsetLeft,
                       p.photo        as photoFile
                from hotel_photos hp
                       inner join photo p on hp.photo_id = p.id and p.is_active = 1
                where hotel_id in (:hotelsIds)
                  and hp.id = (select hp2.id
                               from hotel_photos hp2
                               where hp.hotel_id = hp2.hotel_id
                                 and hp2.is_active = 1
                               order by list_order
                               limit 1)
            ');
        } catch (DBALException $e) {
            return [];
        }
        $stmt->bindValue('hotelsIds', implode(',', $hotelsIds));
        $stmt->execute();

        $queryResult = $stmt->fetchAll();

        $mapPhotos = [];

        foreach ($queryResult as $row) {
            $hotelMapPhotoDto = new HotelMapPhotoDto();
            $hotelMapPhotoDto->hotelId = $row['hotelId'];
            $hotelMapPhotoDto->photoId = $row['photoId'];
            $hotelMapPhotoDto->areaWidth = $row['areaWidth'];
            $hotelMapPhotoDto->areaHeight = $row['areaHeight'];
            $hotelMapPhotoDto->offsetTop = $row['offsetTop'];
            $hotelMapPhotoDto->offsetLeft = $row['offsetLeft'];
            $hotelMapPhotoDto->photoFile = $row['photoFile'];

            $mapPhotos[$hotelMapPhotoDto->hotelId] = $hotelMapPhotoDto;
        }

        return $mapPhotos;
    }
}
