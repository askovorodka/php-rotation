<?php

namespace AppBundle\DataProvider\Dashboard;

use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use AppBundle\DataProvider\AbstractCollectionDataProvider;
use AppBundle\Entity\Hotel;
use AppBundle\Repository\HotelRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Возвращает массив отелей без номеров
 *
 * @package AppBundle\DataProvider
 */
final class HotelsWithoutPhotosCollectionDataProvider extends AbstractCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * DealOffersWithoutHotelCollectionDataProvider constructor.
     *
     * @param ManagerRegistry   $managerRegistry
     * @param array             $collectionExtensions
     * @param RequestStack|null $requestStack
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        array $collectionExtensions = [],
        RequestStack $requestStack
    ) {
        parent::__construct($managerRegistry, $collectionExtensions);
        $this->requestStack = $requestStack;
    }

    /**
     * @inheritdoc
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return
            $resourceClass === Hotel::class
            && $operationName === 'get_hotels_without_photos';
    }

    /**
     * @inheritdoc
     */
    protected function createQueryBuilder(string $resourceClass, string $operationName = null, array $context = [])
    {
        $searchQuery = null;
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $searchQuery = $request->get('query');
        }

        /** @var HotelRepository $hotelRepository */
        $hotelRepository = $this->managerRegistry->getRepository($resourceClass);

        $baseQuery = $hotelRepository->getHotelsWithoutPhotosQuery('h', $searchQuery);
        $baseQuery->select('h.id');
        $ids = array_map('current', $baseQuery->getQuery()->getResult());

        return $hotelRepository->createQueryBuilder('h')
            ->andWhere('h.id in (:ids)')
            ->setParameter('ids', $ids);
    }
}
