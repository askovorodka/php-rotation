<?php

namespace AppBundle\Services\HotelMap;

/**
 * Интерефейс класса, предоставляющего карту отелей
 *
 * Interface HotelMapServiceInterface
 *
 * @package AppBundle\Services\HotelMap
 */
interface HotelMapServiceInterface
{
    /**
     * Возвращает карту отелей для переданной локации
     *
     * @param string|null $area
     * @return HotelMapPointsCollection
     */
    public function getHotelMap(string $area = null): HotelMapPointsCollection;
}
