<?php

namespace AppBundle\Services\HotelsSearch;

use AppBundle\Entity\Hotel;

/**
 * Класс для представления данных о найденном отеле
 *
 * @package AppBundle\Services\HotelsSearch
 */
class FoundHotel
{
    /** @var int */
    protected $hotelId;
    /** @var FoundHotelMinPrice */
    protected $minPrice;

    /**
     * FoundHotel constructor.
     *
     * @param int $hotelId
     * @param FoundHotelMinPrice $minPrice
     */
    public function __construct(int $hotelId, FoundHotelMinPrice $minPrice)
    {
        $this->hotelId = $hotelId;
        $this->minPrice = $minPrice;
    }

    /**
     * @return int
     */
    public function getHotelId()
    {
        return $this->hotelId;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->minPrice ? $this->minPrice->getPriceAfterDiscount() : null;
    }

    /**
     * @return int
     */
    public function getDiscount()
    {
        return $this->minPrice ? $this->minPrice->getDiscount() : null;
    }

    /**
     * @return float
     */
    public function getOriginalPrice()
    {
        return $this->minPrice ? $this->minPrice->getOriginalPrice() : null;
    }

    /**
     * Метод заполняет цену и скидку для переданного отеля
     *
     * @param Hotel $hotel
     */
    public function fillHotelMinPrice(Hotel $hotel)
    {
        if (!$hotel->getId() === $this->hotelId) {
            throw new \LogicException('Несоответствие идентификаторов отеля и результата поиска');
        }

        if (!$this->minPrice) {
            throw new \LogicException('Поле minPrice должно быть заполнено');
        }

        $hotel->setMinPrice($this->getPrice());
        $hotel->setMinOriginalPrice($this->getOriginalPrice());
        $hotel->setDiscount($this->getDiscount());
    }
}
