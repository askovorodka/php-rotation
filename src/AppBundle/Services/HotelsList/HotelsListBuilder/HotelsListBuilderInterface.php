<?php

namespace AppBundle\Services\HotelsList\HotelsListBuilder;

use AppBundle\Services\HotelsList\HotelsList;

interface HotelsListBuilderInterface
{
    /**
     * @return string
     */
    public function getListName(): string;

    /**
     * @return HotelsList
     */
    public function buildList(): HotelsList;
}
