<?php

namespace AppBundle\Services\HotelsList\HotelsListBuilder;

use AppBundle\Services\HotelsList\HotelsListServiceInterface;

class HotelCategoryHotelsListBuilder extends AbstractHotelsListBuilder
{
    function getListName(): string
    {
        return HotelsListServiceInterface::HOTEL_CATEGORY_LIST_NAME;
    }

    protected function getQuery(): string
    {
        return 'select distinct hwmp.hotel_category_id, hc.title, hwmp.id
            from hotel_with_min_price hwmp
                inner join hotel_category hc on hwmp.hotel_category_id = hc.id';
    }

    protected function getCategoryId(array $row)
    {
        return $row['hotel_category_id'];
    }

    protected function getCategoryData(array $row): array
    {
        return [
            'title' => $row['title'],
        ];
    }

    protected function getHotelId(array $row)
    {
        return $row['id'];
    }
}
