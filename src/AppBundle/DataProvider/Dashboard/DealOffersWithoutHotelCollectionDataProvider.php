<?php

namespace AppBundle\DataProvider\Dashboard;

use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use AppBundle\DataProvider\AbstractCollectionDataProvider;
use AppBundle\Entity\DealOffer;
use AppBundle\Repository\DealOfferRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Возвращает массив акций без привязки к отелям
 *
 * @package AppBundle\DataProvider
 */
final class DealOffersWithoutHotelCollectionDataProvider extends AbstractCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
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
            $resourceClass === DealOffer::class
            && $operationName === 'get_deal_offers_without_hotel';
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

        /** @var DealOfferRepository $dealOfferRepository */
        $dealOfferRepository = $this->managerRegistry->getRepository($resourceClass);

        return $dealOfferRepository->getDealOffersWithoutHotelQuery('do', $searchQuery);
    }
}
