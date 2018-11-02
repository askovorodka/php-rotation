<?php

namespace AppBundle\Services\HotelsList\HotelsListBuilder;

use AppBundle\Services\HotelsList\HotelsListServiceInterface;

class HotelAmenityHotelsListBuilder extends AbstractHotelsListBuilder
{
    function getListName(): string
    {
        return HotelsListServiceInterface::HOTEL_AMENITY_LIST_NAME;
    }

    protected function getQuery(): string
    {
        return 'select distinct hawp.amenity_id, a.title, a.sysname, hawp.id
            from hotel_amenities_with_price hawp
            inner join amenity a on hawp.amenity_id = a.id';
    }

    protected function getCategoryId(array $row)
    {
        return $row['amenity_id'];
    }

    protected function getCategoryData(array $row): array
    {
        return [
            'title' => $row['title'],
            'sysname' => $row['sysname'],
        ];
    }

    protected function getHotelId(array $row)
    {
        return $row['id'];
    }
}
