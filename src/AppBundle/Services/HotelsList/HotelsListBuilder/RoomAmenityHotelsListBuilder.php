<?php

namespace AppBundle\Services\HotelsList\HotelsListBuilder;

use AppBundle\Services\HotelsList\HotelsListServiceInterface;

class RoomAmenityHotelsListBuilder extends AbstractHotelsListBuilder
{
    function getListName(): string
    {
        return HotelsListServiceInterface::ROOM_AMENITY_LIST_NAME;
    }

    protected function getQuery(): string
    {
        return 'select distinct rawp.amenity_id, a.title, a.sysname, rawp.id
            from room_amenities_with_price rawp
                inner join amenity a on rawp.amenity_id = a.id';
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
