<?php

namespace AppBundle\DataProvider\HotelsCatalog;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use AppBundle\DataProvider\AbstractCollectionDataProvider;
use AppBundle\Entity\Hotel;
use AppBundle\Services\HotelsCatalogV2\HotelsCatalogServiceInterface;
use AppBundle\Services\HotelsSearch\FoundHotels;
use AppBundle\Dto\HotelsSearch\SearchDtoAssembler;
use Common\Doctrine\Extensions\Field;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Collection provider для фильтра отелей
 *
 * @package AppBundle\DataProvider
 */
final class HotelsSearchCollectionDataProvider extends AbstractCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var RequestStack */
    protected $requestStack;

    /** @var HotelsCatalogServiceInterface */
    protected $hotelsCatalogService;

    /** @var FoundHotels */
    protected $hotelsSearchResult;

    /**
     * HotelsSearchCollectionDataProvider constructor.
     *
     * @param ManagerRegistry                                                                     $managerRegistry
     * @param QueryCollectionExtensionInterface[]|ContextAwareQueryCollectionExtensionInterface[] $collectionExtensions
     * @param RequestStack                                                                        $requestStack
     * @param HotelsCatalogServiceInterface                                                       $hotelsCatalogService
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        array $collectionExtensions = [],
        RequestStack $requestStack,
        HotelsCatalogServiceInterface $hotelsCatalogService
    ) {
        $this->requestStack = $requestStack;
        $this->hotelsCatalogService = $hotelsCatalogService;

        // Убираем FilterEagerLoadingExtension - он ломает сформированный QueryBuilder
        $collectionExtensions = array_filter($collectionExtensions, function ($collectionExtension) {
            return get_class($collectionExtension) !== 'ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\FilterEagerLoadingExtension';
        });

        parent::__construct($managerRegistry, $collectionExtensions);
    }

    /**
     * @inheritdoc
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return
            $resourceClass === Hotel::class
            && $operationName === 'hotels_catalog.search';
    }

    /**
     * @inheritdoc
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        // Убираем фильтры - далее они будут вручную взяты из request
        $context['filters'] = [];

        /** @var Paginator $result */
        if (
            ($result = parent::getCollection($resourceClass, $operationName, $context))
            && $this->hotelsSearchResult
        ) {
            foreach ($result->getIterator() as $hotel) {
                /** @var Hotel $hotel */
                if (($foundHotel = $this->hotelsSearchResult->getFoundHotelById($hotel->getId()))) {
                    $foundHotel->fillHotelMinPrice($hotel);
                }

                // Оставляем только активные фотографии
                foreach ($hotel->getHotelPhotos() as $hotelPhoto) {
                    if (!($hotelPhoto->getIsActive() && $hotelPhoto->getPhoto()->getIsActive())) {
                        $hotel->removeHotelPhoto($hotelPhoto);
                    }
                }
            }

        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function createQueryBuilder(string $resourceClass, string $operationName = null, array $context = [])
    {
        $searchDtoAssembler = new SearchDtoAssembler($this->requestStack->getCurrentRequest());

        $this->hotelsSearchResult = $this->hotelsCatalogService->search($searchDtoAssembler->assemble());

        if ($this->hotelsSearchResult->count()) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->getConnection()->exec(
                'SET sql_mode=(SELECT REPLACE(@@sql_mode,\'ONLY_FULL_GROUP_BY\',\'\'))'
            );

            $doctrineConfig = $entityManager->getConfiguration();
            $doctrineConfig->addCustomStringFunction('FIELD', Field::class);

            $hotelsIds = $this->hotelsSearchResult->getHotelsIds();

            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = $entityManager->getRepository(Hotel::class)->createQueryBuilder('h');
            $queryBuilder
                ->select('h, field(h.id, :ids) as HIDDEN field')
                ->andWhere($queryBuilder->expr()->in('h.id', ':ids'))
                ->setParameter('ids', $hotelsIds)
                // Сортировку делаем по поярдку следования отелей в результате поиска
                ->orderBy('field');

            return $queryBuilder;
        }

        return null;
    }
}
