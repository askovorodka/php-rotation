<?php

namespace AppBundle\Services\HotelsCatalog;

use AppBundle\Entity\AmenityCategory;

/**
 * Класс для представления данных для отображения фильтра на странице каталога отелей
 *
 * @package AppBundle\Services\HotelsCatalog
 */
class FiltersData implements \JsonSerializable
{
    /** @var array */
    protected $areas;
    /** @var int */
    protected $minPrice;
    /** @var int */
    protected $maxPrice;
    /** @var array */
    protected $hotelCategories;
    /** @var array */
    protected $amenities;
    /** @var HotelsCountersData */
    protected $counters;

    /**
     * FiltersData constructor.
     *
     * @param array $areas
     * @param int $minPrice
     * @param int $maxPrice
     * @param array $hotelCategories
     * @param array $amenities
     * @param HotelsCountersData $counters
     */
    public function __construct(
        array $areas,
        int $minPrice,
        int $maxPrice,
        array $hotelCategories,
        array $amenities,
        HotelsCountersData $counters
    ) {
        $this->areas = $areas;
        $this->minPrice = $minPrice;
        $this->maxPrice = $maxPrice;
        $this->hotelCategories = $hotelCategories;
        $this->amenities = $amenities;
        $this->counters = $counters;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'areas' => $this->areas,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'hotelCategories' => $this->hotelCategories,
            'hotelAmenities' => $this->amenities[AmenityCategory::HOTEL_AMENITIES_CATEGORY_SYSTEM_NAME] ?? [],
            'roomAmenities' => $this->amenities[AmenityCategory::ROOM_AMENITIES_CATEGORY_SYSTEM_NAME] ?? [],
            'counters' => $this->counters,
        ];
    }
}
