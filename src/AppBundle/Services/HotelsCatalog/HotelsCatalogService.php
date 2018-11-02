<?php

namespace AppBundle\Services\HotelsCatalog;

use AppBundle\Entity\Amenity;
use AppBundle\Entity\AmenityCategory;
use AppBundle\Entity\DealOffer;
use AppBundle\Entity\Hotel;
use AppBundle\Entity\HotelCategory;
use AppBundle\Repository\AmenityRepository;
use AppBundle\Services\HotelsSearch\FoundHotel;
use AppBundle\Services\HotelsSearch\FoundHotels;
use AppBundle\Services\HotelsSearch\HotelsSearchService;
use AppBundle\Services\SphinxSearchService;
use Common\Doctrine\Extensions\Field;
use Doctrine\ORM\Query\Expr\Join;
use Javer\SphinxBundle\Sphinx\Manager as SphinxManager;
use Javer\SphinxBundle\Sphinx\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Сервис, предоставляющий данные для каталога отелей
 *
 * @package AppBundle\Services\HotelsCatalog
 */
class HotelsCatalogService
{
    /** Поля, по которым возможна сортировка */
    const AVAILABLE_ORDER_FIELDS = ['price', 'discount'];

    /** @var RegistryInterface */
    protected $registry;
    /** @var SphinxManager */
    protected $sphinxSearchService;
    /** @var HotelsSearchService */
    protected $hotelsSearchService;

    /**
     * Приоритетные напрвления отелей, которые выводятся в начале списка направлений
     *
     * @var string[]
     */
    private $priorityAreas;

    /**
     * HotelsCatalogService constructor.
     *
     * @param RegistryInterface   $registry ,
     * @param SphinxSearchService $sphinxSearchService
     * @param HotelsSearchService $hotelsSearchService
     * @param string[]            $priorityAreas
     */
    public function __construct(
        RegistryInterface $registry,
        SphinxSearchService $sphinxSearchService,
        HotelsSearchService $hotelsSearchService,
        array $priorityAreas = []
    ) {
        $this->registry = $registry;
        $this->sphinxSearchService = $sphinxSearchService;
        $this->hotelsSearchService = $hotelsSearchService;
        $this->priorityAreas = $priorityAreas;
    }

    /**
     * Выполняет поиск и сортировку отелей на основе переданного поискового запроса
     *
     * @param SearchDto $searchDto
     *
     * @return FoundHotels
     *
     * @throws \Exception
     */
    public function search(SearchDto $searchDto)
    {
        $foundHotels = $this->hotelsSearchService->findBySearchDto($searchDto);

        if ($foundHotels->count()) {
            $this->orderFoundHotels($searchDto, $foundHotels);
        }

        return $foundHotels;
    }

    /**
     * Возвращает данные для построения фильтра на странице каталога отелей
     *
     * @return FiltersData
     * @throws \Exception
     */
    public function getFiltersData(): FiltersData
    {
        /** @var Query $byRoomsAmenitiesSearchQuery */
        $pricesSearchQuery = $this->sphinxSearchService->createQuery();
        $pricesSearchQuery
            ->select(
                'MIN(price) AS minimum',
                'MAX(price) AS maximum'
            )
            ->from(SphinxSearchService::BY_HOTELS_ROOM_AMENITIES_SEARCH_INDEX)
            ->limit(SphinxSearchService::INFINITE_LIMIT);
        $pricesSearchQueryResult = $pricesSearchQuery->getResults()[0];

        return new FiltersData(
            $this->getAreas(),
            $pricesSearchQueryResult['minimum'],
            $pricesSearchQueryResult['maximum'],
            $this->getHotelCategories(),
            $this->getAmenities(),
            $this->getCounters(new SearchDto())
        );
    }

    /**
     * Возвращает доступные расположения отелей
     *
     * @return array
     */
    protected function getAreas(): array
    {
        $this->registry->getEntityManager()->getConfiguration()->addCustomStringFunction('FIELD', Field::class);

        $query = $this->registry->getEntityManager()->getRepository(Hotel::class)
            ->createQueryBuilder('h')
            ->select('h.administrativeArea')
            ->innerJoin('h.dealOffers', 'd', Join::WITH, 'd.isActive = 1')
            ->where('h.isActive = 1')
            ->andWhere('h.isProduction = 1')
            ->addOrderBy("FIELD(h.administrativeArea, :priority_areas_reversed)", "DESC")
            ->addOrderBy('h.administrativeArea', 'ASC')
            ->distinct()
            ->setParameter('priority_areas_reversed', array_reverse($this->priorityAreas));

        $data = $query->getQuery()->getScalarResult();
        $areas = array_column($data, 'administrativeArea');

        return array_values(array_filter($areas));
    }

    /**
     * Возвращает доступные категории отелей
     *
     * @return array
     */
    protected function getHotelCategories(): array
    {
        /**
         * получаем список активных DO связанных с отелями
         *
         * @var array $activeDealOffers
         */
        $activeDealOffers = $this->registry->getEntityManager()
            ->getRepository(DealOffer::class)
            ->getDealOfferIdsWithHotels();
        /**
         * находим id активных отелей
         */
        $activeHotelsIds = array_column($activeDealOffers, 'hotelId');

        /**
         * находим активные категории
         */
        $hotelCategories = $this->registry->getEntityManager()
            ->getRepository(HotelCategory::class)
            ->getActiveByHotelsIds($activeHotelsIds);

        return array_map(function ($hotelCategory) {
            /** @var HotelCategory $hotelCategory */
            return [
                'id' => $hotelCategory->getId(),
                'title' => $hotelCategory->getTitle(),
            ];
        }, $hotelCategories);
    }

    /**
     * Возвращает доступные удобства отелей и номеров
     *
     * @return array
     */
    protected function getAmenities(): array
    {
        $amenities = [];

        $em = $this->registry->getEntityManager();
        /** @var AmenityRepository $amenityRepository */
        $amenityRepository = $em->getRepository(Amenity::class);

        /** @var AmenityCategory[] $amenityCategories */
        $amenityCategories = $em->getRepository(AmenityCategory::class)->findAll();

        /**
         * получаем список активных DO связанных с отелями
         *
         * @var array $activeDealOffers
         */
        $activeDealOffersIdsWithHotels = $this->registry->getEntityManager()
            ->getRepository(DealOffer::class)
            ->getDealOfferIdsWithHotels();
        /**
         * находим id активных DO
         */
        $activeDealOffersIds = array_column($activeDealOffersIdsWithHotels, 'id');

        /**
         * находим id активных отелей
         */
        $activeHotelsIds = array_column($activeDealOffersIdsWithHotels, 'hotelId');

        foreach ($amenityCategories as $amenityCategory) {
            /** @var AmenityCategory $amenityCategory */
            if ($amenityCategory->getSystemName() == AmenityCategory::HOTEL_AMENITIES_CATEGORY_SYSTEM_NAME) {
                $amenities[AmenityCategory::HOTEL_AMENITIES_CATEGORY_SYSTEM_NAME] = array_map(
                    [$this, 'prepareAmenityToResponse'],
                    $amenityRepository->getActiveByCategoryAndHotelsIds($amenityCategory, $activeHotelsIds));
            } elseif ($amenityCategory->getSystemName() == AmenityCategory::ROOM_AMENITIES_CATEGORY_SYSTEM_NAME) {
                $amenities[AmenityCategory::ROOM_AMENITIES_CATEGORY_SYSTEM_NAME] = array_map(
                    [$this, 'prepareAmenityToResponse'],
                    $amenityRepository->getActiveByCategoryAndDealOffers($amenityCategory, $activeDealOffersIds)
                );
            }
        }

        return $amenities;
    }

    /**
     * подготавливает формат Amenity для ответа
     *
     * @param $amenity
     * @return array
     */
    private function prepareAmenityToResponse($amenity): array
    {
        return [
            'id' => $amenity->getId(),
            'title' => $amenity->getTitle(),
            'sysname' => $amenity->getSysname(),
        ];
    }

    /**
     * Возвращает счетчики отелей на основе переданного поискового запроса
     *
     * @param SearchDto $searchDto
     *
     * @return HotelsCountersData
     *
     * @throws \Exception
     */
    public function getCounters(SearchDto $searchDto)
    {
        $foundHotelsIds = $this->hotelsSearchService->findBySearchDto($searchDto)->getHotelsIds();

        // Если не было найдено отелей, нет смысла в подсчетах - возвращает пустой результат
        if (!$foundHotelsIds) {
            return new HotelsCountersData();
        }

        $totalItems = count($foundHotelsIds);
        $hotelAmenitiesCounters = $this->getHotelAmenitiesCounters($searchDto, $foundHotelsIds);
        $roomAmenitiesCounters = $this->getRoomAmenitiesCounters($searchDto, $foundHotelsIds);
        $hotelCategoriesCounters = $this->getHotelCategoriesCounters($searchDto, $foundHotelsIds);
        $administrativeAreasCounters = $this->getAdministrativeAreasCounters($searchDto, $foundHotelsIds);

        return new HotelsCountersData(
            $hotelAmenitiesCounters,
            $roomAmenitiesCounters,
            $hotelCategoriesCounters,
            $administrativeAreasCounters,
            $totalItems
        );
    }

    /**
     * @param SearchDto $searchDto
     * @param array     $foundHotelsIds
     * @return array
     */
    private function getHotelAmenitiesCounters(SearchDto $searchDto, array $foundHotelsIds)
    {
        return $this->getFieldCounters(
            $searchDto,
            $foundHotelsIds,
            'amenity_id',
            SphinxSearchService::BY_HOTEL_AMENITIES_SEARCH_INDEX
        );
    }

    /**
     * @param SearchDto $searchDto
     * @param array     $foundHotelsIds
     * @return array
     */
    private function getRoomAmenitiesCounters(SearchDto $searchDto, array $foundHotelsIds)
    {
        return $this->getFieldCounters(
            $searchDto,
            $foundHotelsIds,
            'amenity_id',
            SphinxSearchService::BY_ROOM_AMENITIES_SEARCH_INDEX
        );
    }

    /**
     * @param SearchDto $searchDto
     * @param array     $foundHotelsIds
     * @return array
     */
    private function getHotelCategoriesCounters(SearchDto $searchDto, array $foundHotelsIds)
    {
        return $this->getFieldCounters(
            $searchDto,
            $foundHotelsIds,
            'category_id',
            SphinxSearchService::BY_ROOM_AMENITIES_SEARCH_INDEX
        );
    }

    /**
     * @param SearchDto|null $searchDto
     * @param array          $foundHotelsIds
     * @return array
     */
    private function getAdministrativeAreasCounters(SearchDto $searchDto, array $foundHotelsIds)
    {
        return $this->getFieldCounters(
            $searchDto,
            $foundHotelsIds,
            'administrative_area',
            SphinxSearchService::BY_ROOM_AMENITIES_SEARCH_INDEX
        );
    }

    /**
     * @param SearchDto|null $searchDto
     * @param array          $foundHotelsIds
     * @param string         $fieldName
     * @param string         $source
     * @return array
     */
    private function getFieldCounters(
        SearchDto $searchDto,
        array $foundHotelsIds,
        string $fieldName,
        string $source
    ) {
        $searchQuery = $this->sphinxSearchService->createQuery();
        $searchQuery
            ->select(
                $fieldName,
                'GROUP_CONCAT(hotel_id) as hotels_ids'
            )
            ->groupBy($fieldName)
            ->from($source)
            ->limit(SphinxSearchService::INFINITE_LIMIT);

        $data = $searchQuery->getResults();
        $counters = [];
        foreach ($data as $row) {
            if (!$row[$fieldName]) {
                continue;
            }

            $hotelsIds = array_unique(explode(',', $row['hotels_ids']));
            if ($searchDto) {
                $hotelsIds = array_intersect($hotelsIds, $foundHotelsIds);
            }

            $counters[] = [
                $fieldName => $row[$fieldName],
                'hotels_count' => count($hotelsIds),
            ];
        }

        return $counters;
    }

    /**
     * Выполняет сортировку массива найденных отелей в соответствии с переданным поисковым запросом
     *
     * @param SearchDto   $searchDto
     * @param FoundHotels $foundHotels
     *
     * @throws \Exception
     */
    protected function orderFoundHotels(SearchDto $searchDto, FoundHotels $foundHotels)
    {
        $items = $foundHotels->getItems();

        $hotelIds = [];
        foreach ($items as $item) {
            $hotelIds[] = $item->getHotelId();
        }

        // Преобразуем ключи массива в строки (array_multisort сохраняет только строковые ключи)
        $stringPrefix = 'str_';
        $modifiedKeys = array_map(function ($key) use ($stringPrefix) {
            return "{$stringPrefix}{$key}";
        }, $hotelIds);
        /** @var FoundHotel[] $items */
        $items = array_combine($modifiedKeys, $items);

        $args = [];
        $orderByFields = array_intersect_key($searchDto->order, array_flip(self::AVAILABLE_ORDER_FIELDS));
        foreach ($orderByFields as $field => $direction) {
            $tmp = [];
            foreach ($items as $key => $item) {
                $tmp[$key] = $item->{'get' . ucfirst($field)}();
            }
            array_push($args, $tmp, $direction);
        }

        $args[] = &$items;

        call_user_func_array('array_multisort', $args);

        $hotelsIds = array_map(function ($key) use ($stringPrefix) {
            return str_replace($stringPrefix, '', $key);
        }, array_keys($items));

        $items = array_combine($hotelsIds, $items);

        // Добавляем элементы в новом порядке
        $foundHotels->clear();
        foreach ($items as $item) {
            $foundHotels->add($item);
        }
    }
}
