<?php

namespace AppBundle\Services\HotelsCatalogV2;

use JsonSerializable;

class FiltersData implements JsonSerializable
{
    /**
     * @var array
     */
    private $areas;

    /**
     * @var array
     */
    private $hotelCategories;

    /**
     * @var array
     */
    private $hotelAmenities;

    /**
     * @var array
     */
    private $roomAmenities;

    /**
     * @var HotelsCountersData
     */
    private $hotelsCountersData;

    /**
     * FiltersData constructor.
     *
     * @param array              $areas
     * @param array              $hotelCategories
     * @param array              $hotelAmenities
     * @param array              $roomAmenities
     * @param HotelsCountersData $hotelsCountersData
     */
    public function __construct(
        array $areas,
        array $hotelCategories,
        array $hotelAmenities,
        array $roomAmenities,
        HotelsCountersData $hotelsCountersData
    ) {
        $this->areas = $areas;
        $this->hotelCategories = $hotelCategories;
        $this->hotelAmenities = $hotelAmenities;
        $this->roomAmenities = $roomAmenities;
        $this->hotelsCountersData = $hotelsCountersData;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'areas' => $this->areas,
            'hotelCategories' => $this->hotelCategories,
            'hotelAmenities' => $this->hotelAmenities,
            'roomAmenities' => $this->roomAmenities,
            'counters' => $this->hotelsCountersData,
        ];
    }
}
