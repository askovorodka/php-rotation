<?php

namespace AppBundle\DataProvider\Dashboard;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use AppBundle\DataProvider\AbstractCollectionDataProvider;
use AppBundle\Entity\Hotel;
use AppBundle\Repository\HotelRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class HotelsDataProvider extends AbstractCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
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
        return $resourceClass === Hotel::class && $operationName === 'get';
    }

    /**
     * @inheritdoc
     */
    protected function createQueryBuilder(
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ) {
        $searchQuery = null;
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $searchQuery = $request->get('query');
        }

        /** @var HotelRepository $hotelRepository */
        $hotelRepository = $this->managerRegistry->getRepository(Hotel::class);

        $query = $hotelRepository->createQueryBuilder('h');

        if ($searchQuery) {
            $hotelRepository->applySearchQuery($query, $searchQuery);
        }

        return $query;
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array $context
     * @return Hotel[]|\Traversable|array[]
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $hotels = parent::getCollection($resourceClass, $operationName, $context);

        foreach ($hotels as $hotel) {
            /** @var Hotel $hotel */
            if ($hotel->getDealOffers()->count() > 2) {
                $hotel->setDealOffers($hotel->getDealOffers()->slice(0, 2));
            }
        }
        return $hotels;
    }
}
