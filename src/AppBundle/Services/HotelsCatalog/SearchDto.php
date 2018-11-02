<?php

namespace AppBundle\Services\HotelsCatalog;

/**
 * Dto для поиска отелей (+ сортировка)
 */
class SearchDto
{
    /** @var string */
    public $administrativeArea;
    /** @var int[] */
    public $hotelCategories;
    /** @var int[] */
    public $hotelAmenities;
    /** @var int[] */
    public $roomAmenities;
    /** @var int */
    public $priceGte;
    /** @var int */
    public $priceLte;
    /** @var string[] */
    public $order;

    /**
     * HotelsSearchDto constructor.
     *
     * @param string $administrativeArea
     * @param int[] $hotelCategories
     * @param int[] $hotelAmenities
     * @param int[] $roomAmenities
     * @param int $priceGte
     * @param int $priceLte
     * @param string[] $order
     */
    public function __construct(
        string $administrativeArea = null,
        array $hotelCategories = [],
        array $hotelAmenities = [],
        array $roomAmenities = [],
        int $priceGte = null,
        int $priceLte = null,
        array $order = []
    ) {
        $this->administrativeArea = $administrativeArea;
        $this->hotelCategories = $hotelCategories;
        $this->hotelAmenities = $hotelAmenities;
        $this->roomAmenities = $roomAmenities;
        $this->priceGte = $priceGte;
        $this->priceLte = $priceLte;
        $this->order = $order;
    }
}
