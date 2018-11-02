<?php

namespace AppBundle\Services\HotelsCatalog;

/**
 * Класс для хранения счетчиков отелей
 *
 * @package AppBundle\Services\HotelsCatalog
 */
class HotelsCountersData implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $byHotelAmenities;

    /**
     * @var array
     */
    protected $byRoomAmenities;

    /**
     * @var array
     */
    private $byHotelCategories;

    /**
     * @var array
     */
    private $byAdministrativeAreas;

    /**
     * @var int
     */
    private $totalItems;

    /**
     * HotelsCountersData constructor.
     *
     * @param array $byHotelAmenities
     * @param array $byRoomAmenities
     * @param array $byHotelCategories
     * @param array $byAdministrativeAreas
     * @param int   $totalItems
     */
    public function __construct(
        array $byHotelAmenities = [],
        array $byRoomAmenities = [],
        array $byHotelCategories = [],
        array $byAdministrativeAreas = [],
        int $totalItems = 0
    ) {
        $this->byHotelAmenities = $byHotelAmenities;
        $this->byRoomAmenities = $byRoomAmenities;
        $this->byHotelCategories = $byHotelCategories;
        $this->byAdministrativeAreas = $byAdministrativeAreas;
        $this->totalItems = $totalItems;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'forHotelAmenities' => $this->byHotelAmenities,
            'forRoomAmenities' => $this->byRoomAmenities,
            'forHotelCategories' => $this->byHotelCategories,
            'forAdministrativeAreas' => $this->byAdministrativeAreas,
            'totalItems' => $this->totalItems,
        ];
    }
}
