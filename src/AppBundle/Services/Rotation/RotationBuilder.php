<?php

namespace AppBundle\Services\Rotation;

use AppBundle\Entity\PinCategory;
use AppBundle\Services\Rotation\Dto\DealOfferDto;
use AppBundle\Services\Rotation\Dto\HotelDto;
use AppBundle\Services\Rotation\Dto\PresetDto;
use AppBundle\Services\Rotation\Filter\FilterCollection;
use AppBundle\Services\Rotation\Order\OrderCollection;
use AppBundle\Services\Rotation\Pin\Dto\PinDto;
use AppBundle\Services\Rotation\Pin\PinCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class RotationBuilder
 *
 * @package AppBundle\Services\Rotation
 */
class RotationBuilder implements RotationBuilderInterface
{

    public const SORTABLE_FIELDS = [
        'administrative_area',
    ];

    public const FILTERABLE_FIELDS = [
        'administrative_area',
    ];

    /**
     * @var Connection
     */
    private $conn;

    /**
     * RotationBuilder constructor.
     *
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @inheritdoc
     */
    public function getCatalog(
        FilterCollection $filterCollection,
        OrderCollection $orderCollection,
        PinCollection $pinCollection
    ): Catalog {
        $catalog = new Catalog();

        //Формируем баннеры
        $bannerHotelsIndexedById = $this->getBannerHotelsIndexedById($pinCollection);
        $presetsIndexedById = $this->getPresetsIndexedById($pinCollection);
        $bannerPinsSorted = $this->getBannerPinsSorted($pinCollection);

        foreach ($bannerPinsSorted as $pin) {
            if ($pinnedHotelId = $pin->pinnedHotelId) {
                $pinnedHotel = $bannerHotelsIndexedById[$pin->pinnedHotelId] ?? null;

                if (!$pinnedHotel) {
                    continue;
                }

                $catalog->addHotelToBannersIfNotExists($pinnedHotel);
            } elseif ($pinnedPresetId = $pin->pinnedPresetId) {
                $pinnedPreset = $presetsIndexedById[$pin->pinnedPresetId] ?? null;

                if (!$pinnedPreset) {
                    continue;
                }

                $catalog->addPresetToBannersIfNotExists($pinnedPreset);
            }
        }

        // Загружаем отели
        $hotelsQb = $this->getHotelsQuery($filterCollection, $orderCollection);
        $hotelsData = $hotelsQb->execute()->fetchAll();

        $hotels = [];
        $hotelsIndexedById = [];
        foreach ($hotelsData as $hotelRawData) {
            $hotel = $this->buildHotelDto($hotelRawData);

            $hotels[] = $hotel;
            $hotelsIndexedById[$hotel->id] = $hotel;
        }

        // Формируем каталог
        $position = 0;
        $skippedHotels = [];

        //Формируем список отелей/подборок
        foreach ($hotels as $hotel) {
            // Если на текущей позиции стоит пин, то вставляем его
            if ($pinnedEl = $pinCollection->getPinByPosition(PinCategory::CATEGORY_CATALOG, $position)) {
                if ($pinnedHotelId = $pinnedEl->pinnedHotelId) {
                    $pinnedHotel = $hotelsIndexedById[$pinnedHotelId];

                    if ($catalog->addHotelToCatalogIfNotExists($pinnedHotel)) {
                        ++$position;
                    }
                } elseif ($pinnedPresetId = $pinnedEl->pinnedPresetId) {
                    $pinnedPreset = $presetsIndexedById[$pinnedPresetId];

                    if ($catalog->addPresetToCatalogIfNotExists($pinnedPreset)) {
                        ++$position;
                    }
                }
            }

            // Проверяем есть ли пин для отеля
            $hotelId = $hotel->id;
            if ($pinCollection->getPinByPinnedHotelId(PinCategory::CATEGORY_CATALOG, $hotelId)) {
                $skippedHotels[$hotelId] = $hotel;
                continue;
            }

            if ($catalog->addHotelToCatalogIfNotExists($hotel)) {
                ++$position;
            }
        }

        foreach ($skippedHotels as $skippedHotel) {
            $catalog->addHotelToCatalogIfNotExists($skippedHotel);
        }

        return $catalog;
    }

    /**
     * @param PinCollection $pinCollection
     * @return HotelDto[]
     */
    private function getBannerHotelsIndexedById(PinCollection $pinCollection): array
    {
        $hotelsIds = [];

        foreach ($pinCollection as $pin) {
            if ($pin->category === PinCategory::CATEGORY_BANNER && $pinnedHotelId = $pin->pinnedHotelId) {
                $hotelsIds[] = $pinnedHotelId;
            }
        }

        if (!$hotelsIds) {
            return [];
        }

        $qb = $this->conn->createQueryBuilder();
        $qb->select(
            'h.id as hotelId',
            'h.sysname as hotelSysname',
            'h.title as hotelTitle',
            'h.administrative_area as hotelAdministrativeArea',
            'h.rating as hotelRating',
            'hwmp.price as minPrice',
            'hwmp.original_price as minOriginalPrice',
            'hwmp.discount',
            'do.id as dealOfferId',
            'do.title as dealOfferTitle',
            'do.title_link as dealOfferTitleLink'
        )->from('hotel_with_min_price', 'hwmp')
            ->innerJoin('hwmp', 'hotel', 'h', 'hwmp.id = h.id')
            ->innerJoin('hwmp', 'deal_offer', 'do', 'hwmp.deal_offer_id = do.id')
            ->where('h.id in (:hotelsIds)')
            ->setParameter('hotelsIds', $hotelsIds, Connection::PARAM_INT_ARRAY);

        $hotelsIndexedById = [];

        $hotelsData = $qb->execute()->fetchAll();
        foreach ($hotelsData as $hotelDataRaw) {
            $hotel = $this->buildHotelDto($hotelDataRaw);
            $hotelsIndexedById[$hotel->id] = $hotel;
        }

        return $hotelsIndexedById;
    }

    /**
     * @param PinCollection $pinCollection
     * @return PresetDto[]
     */
    private function getPresetsIndexedById(PinCollection $pinCollection): array
    {
        $presetsIds = [];
        foreach ($pinCollection as $pin) {
            if ($presetId = $pin->pinnedPresetId) {
                $presetsIds[] = $presetId;
            }
        }

        if (!$presetsIds) {
            return [];
        }

        $qb = $this->conn->createQueryBuilder();
        $qb->select(
            'p.id',
            'p.sysname',
            'p.title',
            'p.description'
        )->from('preset', 'p')
            ->where('id in (:presetsIds)')
            ->setParameter('presetsIds', $presetsIds, Connection::PARAM_INT_ARRAY);

        $presetsIndexedById = [];

        $presetsData = $qb->execute()->fetchAll();
        foreach ($presetsData as $presetRawData) {
            $preset = $this->buildPresetDto($presetRawData);
            $presetsIndexedById[$preset->id] = $preset;
        }

        return $presetsIndexedById;
    }

    /**
     * @param PinCollection $pinCollection
     * @return PinDto[]
     */
    private function getBannerPinsSorted(PinCollection $pinCollection): array
    {
        $pins = [];

        foreach ($pinCollection as $pin) {
            if ($pin->category === PinCategory::CATEGORY_BANNER) {
                $pins[] = $pin;
            }
        }

        usort($pins, function (PinDto $pinA, PinDto $pinB) {
            if ($pinA->position === $pinB->position) {
                return 0;
            }
            return ($pinA->position < $pinB->position) ? -1 : 1;
        });

        return $pins;
    }

    /**
     * @param FilterCollection $filterCollection
     * @param OrderCollection  $orderCollection
     * @return QueryBuilder
     */
    private function getHotelsQuery(
        FilterCollection $filterCollection,
        OrderCollection $orderCollection
    ): QueryBuilder {
        $qb = $this->conn->createQueryBuilder();

        $qb->select(
            'h.id as hotelId',
            'h.sysname as hotelSysname',
            'h.title as hotelTitle',
            'h.administrative_area as hotelAdministrativeArea',
            'h.rating as hotelRating',
            'hwmp.price as minPrice',
            'hwmp.original_price as minOriginalPrice',
            'hwmp.discount',
            'do.id as dealOfferId',
            'do.title as dealOfferTitle',
            'do.title_link as dealOfferTitleLink'
        )->from('hotel_with_min_price', 'hwmp')
            ->innerJoin('hwmp', 'hotel', 'h', 'hwmp.id = h.id')
            ->innerJoin('hwmp', 'deal_offer', 'do', 'hwmp.deal_offer_id = do.id');

        foreach ($filterCollection as $filter) {
            $field = $filter->field;
            $value = $filter->value;

            if (!\in_array($field, self::FILTERABLE_FIELDS, true)) {
                continue;
            }

            $qb->orWhere("$field = :$field")
                ->setParameter($field, $value);
        }

        foreach ($orderCollection as $num => $orders) {
            $conditions = [];

            foreach ($orders as $order) {
                if ($order->value !== null) {
                    $parameterName = "{$order->field}_$num";
                    $conditions[] = "h.{$order->field} = :$parameterName";

                    $qb->setParameter($parameterName, $order->value);
                } else {
                    $qb->addOrderBy($order->field, $order->direction);
                }
            }

            $conditionStr = implode(' or ', $conditions);

            $qb->addSelect("if ($conditionStr, 1, 0) as order_field_$num")
                ->addOrderBy("order_field_$num", 'desc');
        }

        return $qb;
    }

    /**
     * @param array $rawData
     * @return HotelDto
     */
    private function buildHotelDto(array $rawData): HotelDto
    {
        $dealOffer = new DealOfferDto(
            $rawData['dealOfferId'],
            $rawData['dealOfferTitle'],
            $rawData['dealOfferTitleLink']
        );

        return new HotelDto(
            $rawData['hotelId'],
            $rawData['hotelSysname'],
            $rawData['hotelTitle'],
            $rawData['hotelAdministrativeArea'],
            round($rawData['hotelRating'], 2),
            $rawData['minPrice'],
            $rawData['minOriginalPrice'],
            $rawData['discount'],
            $dealOffer
        );
    }

    /**
     * @param array $rawData
     * @return PresetDto
     */
    private function buildPresetDto(array $rawData): PresetDto
    {
        return new PresetDto(
            $rawData['id'],
            $rawData['sysname'],
            $rawData['title'],
            $rawData['description']
        );
    }

}
