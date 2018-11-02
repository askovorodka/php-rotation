<?php

namespace AppBundle\Services\HotelsList;

class HotelsListCollection
{
    /**
     * @var HotelsList[]
     */
    private $lists = [];

    public function addList(string $name, HotelsList $list): self
    {
        $this->lists[$name] = $list;
        return $this;
    }

    public function getList(string $name): HotelsList
    {
        return $this->lists[$name];
    }

    public function getCounters(array $hotelsIdsFlipped = []): array
    {
        $counters = [];
        foreach ($this->lists as $listName => $list) {
            $counters[$listName] = $list->getCounters($hotelsIdsFlipped);
        }

        return $counters;
    }
}
