<?php

namespace AppBundle\Services\HotelsSearch;

use AppBundle\Services\HotelsCatalog\SearchDto;
use AppBundle\Services\SphinxSearchService;
use \Javer\SphinxBundle\Sphinx\Query;

/**
 * Сервис для выполнения поиска отелей
 *
 * @package AppBundle\Services\HotelsSearch
 */
class HotelsSearchService
{
    /**
     * @var SphinxSearchService
     */
    protected $sphinxSearchService;

    /**
     * @var FoundHotels|null
     */
    private $searchCache;

    /**
     * HotelsSearchService constructor.
     *
     * @param sphinxSearchService $sphinxSearchService
     */
    public function __construct(SphinxSearchService $sphinxSearchService)
    {
        $this->sphinxSearchService = $sphinxSearchService;
    }

    /**
     * Выполняет поиск отелей на основе переданного поискового запроса
     *
     * @param SearchDto $searchDto
     *
     * @return FoundHotels
     *
     * @throws \Exception
     */
    public function findBySearchDto(SearchDto $searchDto)
    {
        if ($this->searchCache) {
            return $this->searchCache;
        }

        /** @var Query $byRoomsAmenitiesSearchQuery */
        $byRoomsAmenitiesSearchQuery = $this->sphinxSearchService->createQuery();
        $byRoomsAmenitiesSearchQuery
            ->select(
                'hotel_id',
                'COUNT(DISTINCT amenity_id) as matched_roomAmenities_count',
                'room_id',
                'GROUP_CONCAT(price_id) AS prices_ids_concat',
                'GROUP_CONCAT(price) AS prices_concat',
                'GROUP_CONCAT(original_price) AS original_prices_concat',
                'GROUP_CONCAT(discount) AS discounts_concat',
                'MIN(price) as min_price'
            )
            ->groupBy('room_id')
            ->from(SphinxSearchService::BY_HOTELS_ROOM_AMENITIES_SEARCH_INDEX)
            ->limit(SphinxSearchService::INFINITE_LIMIT);

        if ($searchDto->administrativeArea) {
            $byRoomsAmenitiesSearchQuery->where('administrative_area', '=', $searchDto->administrativeArea);
        }

        if (count($searchDto->hotelCategories)) {
            $byRoomsAmenitiesSearchQuery->where('category_id', 'IN', $searchDto->hotelCategories);
        }

        if (count($searchDto->roomAmenities)) {
            $byRoomsAmenitiesSearchQuery
                ->where('amenity_id', 'IN', $searchDto->roomAmenities)
                ->having('matched_roomAmenities_count', '=', count($searchDto->roomAmenities));
        }

        if ($searchDto->priceGte || $searchDto->priceLte) {
            if ($searchDto->priceGte) {
                $byRoomsAmenitiesSearchQuery->where('price', '>=', $searchDto->priceGte);
            }

            if ($searchDto->priceLte) {
                $byRoomsAmenitiesSearchQuery->where('price', '<=', $searchDto->priceLte);
            }
        }

        // Данные с группировкой по комнатам
        $rooms = $byRoomsAmenitiesSearchQuery->getResults();

        $result = new FoundHotels();
        if (count($rooms)) {
            $foundHotelsIds = array_unique(array_column($rooms, 'hotel_id'));
            if (count($searchDto->hotelAmenities)) {
                // Если в запросе указаны удобства отелей, используем второй индекс для фильтрации по ним
                /** @var Query $byHotelAmenitiesSearchQuery */
                $byHotelAmenitiesSearchQuery = $this->sphinxSearchService->createQuery()
                    ->select(
                        'hotel_id',
                        'COUNT(DISTINCT amenity_id) as matched_hotelAmenities_count'
                    )
                    ->groupBy('hotel_id')
                    ->from(SphinxSearchService::BY_HOTEL_AMENITIES_SEARCH_INDEX)
                    ->where('hotel_id', 'IN', $foundHotelsIds)
                    ->where('amenity_id', 'IN', $searchDto->hotelAmenities)
                    ->having('matched_hotelAmenities_count', '=', count($searchDto->hotelAmenities))
                    ->limit(SphinxSearchService::INFINITE_LIMIT);

                $foundHotelsIds = array_unique(array_column($byHotelAmenitiesSearchQuery->getResults(), 'hotel_id'));
            }

            // Фильтруем по оставшимся отелям и группируем результаты поиска по идентификаторам отеля
            $hotelsRooms = [];
            foreach ($rooms as $room) {
                if (in_array($room['hotel_id'], $foundHotelsIds)) {
                    $hotelsRooms[$room['hotel_id']][] = $room;
                }
            }

            // Собираем цены со всех найденных номеров
            foreach ($hotelsRooms as $hotelId => $hotelRooms) {
                $result->add(
                    new FoundHotel($hotelId, $this->getHotelMinPrice($hotelRooms))
                );
            }
        }

        return $this->searchCache = $result;
    }

    /**
     * Выполняет поиск отеля по переданному идентификатору
     *
     * @param $hotelId
     *
     * @return FoundHotel|null
     */
    public function findByHotelId($hotelId)
    {
        $searchQuery = $this->sphinxSearchService->createQuery();
        $searchQuery
            ->select(
                'GROUP_CONCAT(price_id) AS prices_ids_concat',
                'GROUP_CONCAT(price) AS prices_concat',
                'GROUP_CONCAT(original_price) AS original_prices_concat',
                'GROUP_CONCAT(discount) AS discounts_concat'
            )
            ->groupBy('hotel_id')
            ->where('hotel_id', '=', $hotelId)
            ->from(SphinxSearchService::BY_HOTELS_ROOM_AMENITIES_SEARCH_INDEX)
            ->limit(SphinxSearchService::INFINITE_LIMIT);

        if (($searchResult = $searchQuery->getResults())) {
            return new FoundHotel($hotelId, $this->getHotelMinPrice($searchResult));
        }

        return null;
    }

    /**
     * Вычисляет минимальную цену отеля
     *
     * @param array $data
     *
     * @return FoundHotelMinPrice
     */
    protected function getHotelMinPrice(array $data)
    {
        $dealOfferPrices = [];
        foreach ($data as $item) {
            $dealOfferPricesIds = explode(',', $item['prices_ids_concat']);
            $prices = explode(',', $item['prices_concat']);
            $originalPrices = explode(',', $item['original_prices_concat']);
            $discounts = explode(',', $item['discounts_concat']);

            foreach ($dealOfferPricesIds as $key => $dealOfferPriceId) {
                if (!isset($dealOfferPrices[$dealOfferPriceId])) {
                    $dealOfferPrices[$dealOfferPriceId] = [
                        'dealOfferPriceId' => $dealOfferPriceId,
                        'price' => $prices[$key],
                        'originalPrice' => $originalPrices[$key],
                        'discount' => $discounts[$key],
                    ];
                }
            }
        }

        // Редьюсим до минимальной цены
        $minPriceData = array_reduce($dealOfferPrices, function (&$carry, $item) {
            if (!$carry) {
                return $item;
            } elseif ($item['price'] <= $carry['price']) {
                if (abs($item['price'] - $carry['price']) < 0.000001) {
                    return $item['originalPrice'] >= $carry['originalPrice'] ? $item : $carry;
                }
                return $item;
            }
            return $carry;
        });

        return new FoundHotelMinPrice(
            $minPriceData['dealOfferPriceId'],
            $minPriceData['originalPrice'],
            $minPriceData['discount'],
            $minPriceData['price']
        );
    }
}
