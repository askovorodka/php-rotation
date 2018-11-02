<?php

namespace AppBundle\Services\HotelsList\HotelsListCollectionStorage;

use AppBundle\Services\HotelsList\HotelsListCollection;

class HotelsListCollectionFileStorage implements HotelsListCollectionStorageInterface
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var HotelsListCollection
     */
    private $listCollection;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function save(HotelsListCollection $listCollection)
    {
        $rawData = serialize($listCollection);
        file_put_contents($this->file, $rawData);

        $this->listCollection = $listCollection;
        return $this;
    }

    public function getListCollection(): HotelsListCollection
    {
        if ($this->listCollection) {
            return $this->listCollection;
        }

        $rawData = file_get_contents($this->file);
        return $this->listCollection = unserialize($rawData);
    }
}
