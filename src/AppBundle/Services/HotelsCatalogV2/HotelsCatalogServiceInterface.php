<?php

namespace AppBundle\Services\HotelsCatalogV2;

use AppBundle\Services\HotelsSearch\FoundHotels;
use AppBundle\Dto\HotelsSearch\SearchDto;

interface HotelsCatalogServiceInterface
{
    public function search(SearchDto $searchDto): FoundHotels;

    public function getFiltersData(): FiltersData;

    public function getCounters(SearchDto $searchDto): HotelsCountersData;
}
