<?php

namespace AppBundle\Services\HotelsCatalogV2;

use AppBundle\Services\HotelsList\HotelsListServiceInterface;
use AppBundle\Services\HotelsSearch\FoundHotels;
use AppBundle\Services\HotelsSearch\HotelsSearchServiceInterface;
use AppBundle\Dto\HotelsSearch\SearchDto;

class HotelsCatalogService implements HotelsCatalogServiceInterface
{
    /**
     * @var HotelsSearchServiceInterface
     */
    private $hotelsSearchService;

    /**
     * @var HotelsListServiceInterface
     */
    private $hotelsListService;

    /**
     * HotelsCatalogService constructor.
     *
     * @param HotelsSearchServiceInterface $hotelsSearchService
     * @param HotelsListServiceInterface   $hotelsListService
     */
    public function __construct(
        HotelsSearchServiceInterface $hotelsSearchService,
        HotelsListServiceInterface $hotelsListService
    ) {
        $this->hotelsSearchService = $hotelsSearchService;
        $this->hotelsListService = $hotelsListService;
    }

    /**
     * @inheritdoc
     */
    public function search(SearchDto $searchDto): FoundHotels
    {
        return $this->hotelsSearchService->search($searchDto);
    }

    /**
     * @inheritdoc
     */
    public function getFiltersData(): FiltersData
    {
        $hotelsListCollection = $this->hotelsListService->getHotelsListCollection();

        $areasList = $hotelsListCollection
            ->getList(HotelsListServiceInterface::ADMINISTRATIVE_AREA_LIST_NAME);
        $areas = $areasList->getCategoriesIds();

        $hotelCategoriesList = $hotelsListCollection
            ->getList(HotelsListServiceInterface::HOTEL_CATEGORY_LIST_NAME);
        $hotelCategories = [];
        foreach ($hotelCategoriesList->getCategoriesIds() as $categoryId) {
            $hotelCategories[] = [
                'id' => $categoryId,
                'title' => $hotelCategoriesList->getCategoryData($categoryId)['title'],
            ];
        }

        $hotelAmenitiesList = $hotelsListCollection
            ->getList(HotelsListServiceInterface::HOTEL_AMENITY_LIST_NAME);
        $hotelAmenities = [];
        foreach ($hotelAmenitiesList->getCategoriesIds() as $categoryId) {
            $categoryData = $hotelAmenitiesList->getCategoryData($categoryId);
            $hotelAmenities[] = [
                'id' => $categoryId,
                'title' => $categoryData['title'],
                'sysname' => $categoryData['sysname'],
            ];
        }

        $roomAmenitiesList = $hotelsListCollection
            ->getList(HotelsListServiceInterface::ROOM_AMENITY_LIST_NAME);
        $roomAmenities = [];
        foreach ($roomAmenitiesList->getCategoriesIds() as $categoryId) {
            $categoryData = $roomAmenitiesList->getCategoryData($categoryId);
            $roomAmenities[] = [
                'id' => $categoryId,
                'title' => $categoryData['title'],
                'sysname' => $categoryData['sysname'],
            ];
        }

        $counters = $this->getCounters(new SearchDto());

        return new FiltersData(
            $areas,
            $hotelCategories,
            $hotelAmenities,
            $roomAmenities,
            $counters
        );
    }

    public function getCounters(SearchDto $searchDto): HotelsCountersData
    {
        $hotelsIds = $this->search($searchDto)->getHotelsIds();
        $rawCounters = $this->hotelsListService->getCounters($hotelsIds);

        $hotelAmenityRawCounters = $rawCounters[HotelsListServiceInterface::HOTEL_AMENITY_LIST_NAME];
        $hotelAmenityCounters = [];
        foreach ($hotelAmenityRawCounters as $amenityId => $cnt) {
            $hotelAmenityCounters[] = [
                'amenity_id' => $amenityId,
                'hotels_count' => $hotelsIds ? $cnt : 0,
            ];
        }

        $roomAmenityRawCounters = $rawCounters[HotelsListServiceInterface::ROOM_AMENITY_LIST_NAME];
        $roomAmenityCounters = [];
        foreach ($roomAmenityRawCounters as $amenityId => $cnt) {
            $roomAmenityCounters[] = [
                'amenity_id' => $amenityId,
                'hotels_count' => $hotelsIds ? $cnt : 0,
            ];
        }

        $hotelCategoryRawCounters = $rawCounters[HotelsListServiceInterface::HOTEL_CATEGORY_LIST_NAME];
        $hotelCategoryCounters = [];
        foreach ($hotelCategoryRawCounters as $categoryId => $cnt) {
            $hotelCategoryCounters[] = [
                'category_id' => $categoryId,
                'hotels_count' => $hotelsIds ? $cnt : 0,
            ];
        }

        $areaRawCounters = $rawCounters[HotelsListServiceInterface::ADMINISTRATIVE_AREA_LIST_NAME];
        $areaCounters = [];
        foreach ($areaRawCounters as $area => $cnt) {
            $areaCounters[] = [
                'administrative_area' => $area,
                'hotels_count' => $hotelsIds ? $cnt : 0,
            ];
        }

        $counters = new HotelsCountersData(
            $hotelAmenityCounters,
            $roomAmenityCounters,
            $hotelCategoryCounters,
            $areaCounters,
            count($hotelsIds)
        );

        return $counters;
    }
}
