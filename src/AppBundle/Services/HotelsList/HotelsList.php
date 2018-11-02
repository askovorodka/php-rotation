<?php

namespace AppBundle\Services\HotelsList;

class HotelsList
{
    private const CATEGORY_DATA_KEY = 'categoryData';
    private const HOTELS_IDS_FLIPPED_KEY = 'hotels';

    private $data = [];

    public function addCategory(string $categoryId, array $categoryData): self
    {
        $this->data[$categoryId][self::CATEGORY_DATA_KEY] = $categoryData;
        return $this;
    }

    public function getCategoryData(string $categoryId)
    {
        return $this->data[$categoryId][self::CATEGORY_DATA_KEY];
    }

    public function categoryExists(string $categoryId): bool
    {
        return isset($this->data[$categoryId]);
    }

    public function addHotel(string $categoryId, int $hotelId): self
    {
        $this->data[$categoryId][self::HOTELS_IDS_FLIPPED_KEY][$hotelId] = true;
        return $this;
    }

    public function getCounters(array $hotelsIdsFlipped = []): array
    {
        $counters = [];

        foreach ($this->data as $categoryId => $category) {
            $hotelsIds = $category[self::HOTELS_IDS_FLIPPED_KEY];
            $foundHotelsIds = $hotelsIdsFlipped ?
                array_intersect_key($hotelsIdsFlipped, $hotelsIds) :
                $hotelsIds;

            $counters[$categoryId] = count($foundHotelsIds);
        }

        return $counters;
    }

    public function getCategoriesIds(): array
    {
        return array_keys($this->data);
    }
}
