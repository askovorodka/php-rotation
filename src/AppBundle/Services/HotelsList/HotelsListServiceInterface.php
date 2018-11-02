<?php

namespace AppBundle\Services\HotelsList;

interface HotelsListServiceInterface
{
    const ADMINISTRATIVE_AREA_LIST_NAME = 'administrative_area';
    const HOTEL_CATEGORY_LIST_NAME = 'hotel_category';
    const HOTEL_AMENITY_LIST_NAME = 'hotel_amenity';
    const ROOM_AMENITY_LIST_NAME = 'room_amenity';

    public function getHotelsListCollection(): HotelsListCollection;

    public function getCounters(array $hotelsIds);
}
