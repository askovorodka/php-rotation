<?php

namespace AppBundle\Dto\HotelsSearch;

class SearchDto
{
    /**
     * @var string
     */
    public $administrativeArea;

    /**
     * @var int[]
     */
    public $hotelCategories;

    /**
     * @var int[]
     */
    public $hotelAmenities;

    /**
     * @var int[]
     */
    public $roomAmenities;

    /**
     * @var int
     */
    public $priceGte;

    /**
     * @var int
     */
    public $priceLte;

    /**
     * @var string[]
     */
    public $order;

    /**
     * SearchDto constructor.
     *
     * @param string   $administrativeArea
     * @param int[]    $hotelCategories
     * @param int[]    $hotelAmenities
     * @param int[]    $roomAmenities
     * @param int      $priceGte
     * @param int      $priceLte
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

    public function __toString()
    {
        return json_encode([
            $this->administrativeArea,
            $this->hotelCategories,
            $this->hotelAmenities,
            $this->roomAmenities,
            $this->priceGte,
            $this->priceLte,
            $this->order
        ]);
    }
}
