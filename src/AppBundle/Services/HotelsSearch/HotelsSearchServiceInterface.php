<?php

namespace AppBundle\Services\HotelsSearch;

use AppBundle\Dto\HotelsSearch\SearchDto;
use AppBundle\Services\HotelsSearch\Exceptions\HotelNotFoundException;

interface HotelsSearchServiceInterface
{
    const AVAILABLE_ORDER_FIELDS = ['price', 'discount'];

    /**
     * @param SearchDto $searchDto
     * @return FoundHotels
     */
    public function search(SearchDto $searchDto): FoundHotels;

    /**
     * @param string $sysname
     * @return FoundHotel
     * @throws HotelNotFoundException
     */
    public function findBySysname(string $sysname): FoundHotel;

    /**
     * @param int $id
     * @return FoundHotel
     * @throws HotelNotFoundException
     */
    public function findById(int $id): FoundHotel;
}
