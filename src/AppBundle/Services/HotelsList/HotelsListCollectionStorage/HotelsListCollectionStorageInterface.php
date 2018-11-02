<?php

namespace AppBundle\Services\HotelsList\HotelsListCollectionStorage;

use AppBundle\Services\HotelsList\HotelsListCollection;

interface HotelsListCollectionStorageInterface
{
    public function save(HotelsListCollection $listCollection);

    public function getListCollection(): HotelsListCollection;
}
