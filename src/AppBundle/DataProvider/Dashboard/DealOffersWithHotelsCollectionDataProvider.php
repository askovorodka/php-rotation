<?php

namespace AppBundle\DataProvider\Dashboard;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use AppBundle\DataProvider\AbstractCollectionDataProvider;
use AppBundle\Entity\DealOffer;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class DealOffersWithHotelCollectionDataProvider
 *
 * @package AppBundle\DataProvider\Dashboard
 */
final class DealOffersWithHotelsCollectionDataProvider extends AbstractCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
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
        return $resourceClass === DealOffer::class && $operationName === 'get_active_deal_offers_with_hotel';
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

        $dealOfferRepository = $this->managerRegistry->getRepository(DealOffer::class);

        $query = $dealOfferRepository->getActiveDealOffersWithHotelsQuery('do', $searchQuery);
        $query->orderBy('do.startAt', 'desc');

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        /** @var DealOffer[] $dealOffers */
        $dealOffers = parent::getCollection($resourceClass, $operationName, $context);

        $now = new \DateTime();
        foreach ($dealOffers as $dealOffer) {
            foreach ($dealOffer->getDealOfferPrices() as $price) {
                if ($price->getValidDate() < $now) {
                    $dealOffer->removeDealOfferPrice($price);
                }
            }
        }

        return $dealOffers;
    }
}
