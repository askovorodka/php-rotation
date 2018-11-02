<?php

namespace AppBundle\Services;

use \Javer\SphinxBundle\Sphinx\Query;
use \Javer\SphinxBundle\Sphinx\Manager as SphinxManager;

/**
 * Сервис, предоставляющий доступ к sphinx
 *
 * @package AppBundle\Services
 */
class SphinxSearchService
{
    /** Основной индекс для поиска (roomAmenities, price, administrative_area, etc.) */
    const BY_ROOM_AMENITIES_SEARCH_INDEX = 'published_hotels_joined_with_roomAmenities_search_index';
    /** Индекс для поиска только по удобствам отеля */
    const BY_HOTEL_AMENITIES_SEARCH_INDEX = 'published_hotels_joined_with_hotelAmenities_search_index';
    /** Основной(обновленный) индекс для поиска (roomAmenities, price, administrative_area, etc.) */
    const BY_HOTELS_ROOM_AMENITIES_SEARCH_INDEX = 'only_published_hotels_joined_with_roomAmenities_search_index';

    /** Ограничение на количество результатов по-умолчанию */
    const INFINITE_LIMIT = 1000000;

    /** @var SphinxManager */
    private $sphinxManager;

    /**
     * SphinxSearchService constructor.
     *
     * @param SphinxManager $sphinx
     */
    public function __construct(SphinxManager $sphinx)
    {
        $this->sphinxManager = $sphinx;
    }

    /**
     * @return Query
     */
    public function createQuery()
    {
        return $this->sphinxManager->createQuery();
    }

    /**
     * @return SphinxManager
     */
    public function getSphinxManager()
    {
        return $this->sphinxManager;
    }
}
