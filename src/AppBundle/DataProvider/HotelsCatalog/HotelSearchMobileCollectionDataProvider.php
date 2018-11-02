<?php

namespace AppBundle\DataProvider\HotelsCatalog;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\FilterEagerLoadingExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use AppBundle\DataProvider\AbstractCollectionDataProvider;
use AppBundle\Entity\Hotel;
use AppBundle\Entity\HotelAmenity;
use AppBundle\Entity\HotelPhoto;
use AppBundle\Helper\MobileRequestHelper;
use AppBundle\Dto\HotelsSearch\SearchDtoAssembler;
use AppBundle\Services\HotelsCatalogV2\HotelsCatalogServiceInterface;
use AppBundle\Services\HotelsSearch\FoundHotel;
use AppBundle\Services\HotelsSearch\FoundHotels;
use AppBundle\Services\PhotoUrlService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\Field;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;

final class HotelSearchMobileCollectionDataProvider extends AbstractCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{

    const MOBILE_OFFSET_PARAMETER = 'startindex';
    const MOBILE_LIMIT_PARAMETER = 'quantity';

    /**
     * @var RequestStack $requestStack
     */
    protected $requestStack;

    /**
     * @var HotelsCatalogServiceInterface $hotelCatalogService
     */
    protected $hotelCatalogService;

    /**
     * @var PhotoUrlService
     */
    protected $photoUrlService;

    /**
     * @var FoundHotels $hotelsSearchResult
     */
    protected $hotelsSearchResult;

    /**
     * @var string $photoLinkPrefix
     */
    private $photoLinkPrefix;

    /**
     * @var string $iconsLinkPrefix
     */
    private $iconsLinkPrefix;

    public function __construct(
        ManagerRegistry $managerRegistry,
        $collectionExtensions = [],
        RequestStack $requestStack,
        HotelsCatalogServiceInterface $hotelsCatalogService,
        PhotoUrlService $photoUrlService,
        $photoLinkPrefix,
        $iconsLinkPrefix
    ) {
        $this->requestStack = $requestStack;
        $this->hotelCatalogService = $hotelsCatalogService;
        $this->photoUrlService = $photoUrlService;
        $this->photoLinkPrefix = $photoLinkPrefix;
        $this->iconsLinkPrefix = $iconsLinkPrefix;
        $collectionExtensions = array_filter($collectionExtensions, function ($collectionExtension) {
            return !$collectionExtension instanceof FilterEagerLoadingExtension;
        });
        parent::__construct($managerRegistry, $collectionExtensions);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Hotel::class && $operationName === 'hotels_catalog_mobile.search';
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $context['filters'] = [];
        $result = $this->getApplyCollections($resourceClass, $operationName, $context);
        if ($result && $this->hotelsSearchResult) {
            /**
             * @var Hotel $hotel
             */
            foreach ($result->getIterator() as $hotel) {
                /**
                 * @var FoundHotel $foundHotel
                 */
                if ($foundHotel = $this->hotelsSearchResult->getFoundHotelById($hotel->getId())) {
                    $foundHotel->fillHotelMinPrice($hotel);
                }
                $this->setAmenitiesIcons($hotel);
                $this->setHotelPhotoUrls($hotel);
            }
        }
        return $result;
    }

    protected function getApplyCollections(string $resourceClass, string $operationName = null, array $context = [])
    {
        if (!($queryBuilder = $this->createQueryBuilder($resourceClass, $operationName, $context))) {
            return [];
        }

        $queryNameGenerator = new QueryNameGenerator();
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);

            if ($extension instanceof PaginationExtension) {
                $this->setMobilePaginationParameters($queryBuilder);
            }

            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass,
                    $operationName, $context)) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    protected function createQueryBuilder(string $resourceClass, string $operationName = null, array $context = [])
    {
        $searchDtoAssembler = new SearchDtoAssembler($this->requestStack->getCurrentRequest());
        $this->hotelsSearchResult = $this->hotelCatalogService->search($searchDtoAssembler->assemble());

        if (!$this->hotelsSearchResult->count()) {
            return null;
        }

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->getConnection()->exec(
            'SET sql_mode=(SELECT REPLACE(@@sql_mode,\'ONLY_FULL_GROUP_BY\',\'\'))'
        );

        $doctrineConfig = $entityManager->getConfiguration();
        $doctrineConfig->addCustomStringFunction('FIELD', Field::class);
        $hotelsIds = $this->hotelsSearchResult->getHotelsIds();

        $queryBuilder = $entityManager->getRepository(Hotel::class)->createQueryBuilder('h');
        $queryBuilder
            ->select('h, field(h.id, :ids) as HIDDEN field')
            ->andWhere($queryBuilder->expr()->in('h.id', ':ids'))
            ->setParameter('ids', $hotelsIds)
            // Сортировку делаем по порядки следования отелей в результате поиска
            ->orderBy('field');

        return $queryBuilder;
    }

    private function setMobilePaginationParameters(QueryBuilder $queryBuilder)
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->query->get(self::MOBILE_OFFSET_PARAMETER)) {
            $queryBuilder->setFirstResult((int)$request->query->get(self::MOBILE_OFFSET_PARAMETER));
        }

        if ($request->query->get(self::MOBILE_LIMIT_PARAMETER)) {
            $queryBuilder->setMaxResults($request->query->get(self::MOBILE_LIMIT_PARAMETER));
        }
    }

    public function setHotelPhotoUrls(Hotel $hotel): void
    {
        /**
         * @var HotelPhoto $hotelPhoto
         */
        foreach ($hotel->getHotelPhotos() as $hotelPhoto) {
            $photoUrl = $this->photoUrlService->getPhotoUrl($hotelPhoto);
            $hotelPhoto->setUrl($photoUrl);
            $hotel->addPhotoUrl($photoUrl);
        }
    }

    /**
     * @param Hotel $hotel
     */
    private function setAmenitiesIcons(Hotel $hotel): void
    {
        /**
         * @var HotelAmenity $amenity
         */
        foreach ($hotel->getHotelAmenities() as $amenity) {
            $icon = $amenity->getAmenity()->getIcon();
            if (!$icon) {
                continue;
            }
            if (preg_match('/^http[s]?:/', $icon)) {
                continue;
            }
            $icon = $this->iconsLinkPrefix . MobileRequestHelper::changePngExtention($icon);
            $amenity->getAmenity()->setIcon($icon);
        }
    }
}
