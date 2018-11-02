<?php

namespace AppBundle\Services\HotelsSearch;

/**
 * Класс для представления данных о минимальной цене отеля
 *
 * @package AppBundle\Services\HotelsSearch
 */
class FoundHotelMinPrice
{
    /** @var int */
    protected $dealOfferPriceId;
    /** @var float */
    protected $originalPrice;
    /** @var int */
    protected $discount;
    /** @var float */
    protected $priceAfterDiscount;

    /**
     * FoundHotelMinPrice constructor.
     *
     * @param int $dealOfferPriceId
     * @param float $originalPrice
     * @param int $discount
     * @param float $priceAfterDiscount
     */
    public function __construct(int $dealOfferPriceId, float $originalPrice, int $discount, float $priceAfterDiscount)
    {
        $this->dealOfferPriceId = $dealOfferPriceId;
        $this->originalPrice = $originalPrice;
        $this->discount = $discount;
        $this->priceAfterDiscount = $priceAfterDiscount;
    }

    /**
     * @return int
     */
    public function getDealOfferPriceId(): int
    {
        return $this->dealOfferPriceId;
    }

    /**
     * @return float
     */
    public function getOriginalPrice(): float
    {
        return $this->originalPrice;
    }

    /**
     * @return int
     */
    public function getDiscount(): int
    {
        return $this->discount;
    }

    /**
     * @return float
     */
    public function getPriceAfterDiscount(): float
    {
        return $this->priceAfterDiscount;
    }
}
