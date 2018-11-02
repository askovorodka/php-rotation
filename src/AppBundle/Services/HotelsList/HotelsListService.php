<?php

namespace AppBundle\Services\HotelsList;

use AppBundle\Services\HotelsList\HotelsListCollectionStorage\HotelsListCollectionStorageInterface;

class HotelsListService implements HotelsListServiceInterface
{
    /**
     * @var HotelsListCollectionStorageInterface
     */
    private $storage;

    /**
     * HotelsListService constructor.
     *
     * @param HotelsListCollectionStorageInterface $storage
     */
    public function __construct(HotelsListCollectionStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @inheritdoc
     */
    public function getHotelsListCollection(): HotelsListCollection
    {
        return $this->storage->getListCollection();
    }

    /**
     * @param int[] $hotelsIds
     * @return array
     */
    public function getCounters(array $hotelsIds = []): array
    {
        $hotelsIdsFlipped = array_flip($hotelsIds);

        return $this->getHotelsListCollection()->getCounters($hotelsIdsFlipped);
    }
}
