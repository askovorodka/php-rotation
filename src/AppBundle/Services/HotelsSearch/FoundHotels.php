<?php

namespace AppBundle\Services\HotelsSearch;

/**
 * Класс для представления результата поиска отелей
 *
 * @package AppBundle\Services\HotelsSearch
 */
class FoundHotels extends \Common\Mapping\AbstractMap
{
    /**
     * @inheritdoc
     */
    public function itemIsValid($item)
    {
        return $item instanceof FoundHotel;
    }

    /**
     * @inheritdoc
     *
     * @param FoundHotel $item1
     * @param FoundHotel $item2
     */
    public function itemsEquals($item1, $item2)
    {
        return $item1->getHotelId() === $item2->getHotelId();
    }

    /**
     * @return FoundHotel[]
     */
    public function getItems()
    {
        return parent::getItems();
    }

    /**
     * @return array Идентификаторы найденных отелей
     */
    public function getHotelsIds()
    {
        return array_keys($this->items);
    }

    /**
     * Возвращает найденный отель по переданному идентификатору
     *
     * @param int $hotelId
     *
     * @return FoundHotel
     */
    public function getFoundHotelById(int $hotelId)
    {
        return $this->items[$hotelId] ?? null;
    }

    /**
     * @param FoundHotel $item
     *
     * @return bool
     */
    protected function _add($item)
    {
        $this->items[$item->getHotelId()] = $item;
        return true;
    }
}
