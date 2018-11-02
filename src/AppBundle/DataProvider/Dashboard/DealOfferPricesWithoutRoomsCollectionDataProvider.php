<?php

namespace AppBundle\DataProvider\Dashboard;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use AppBundle\DataProvider\AbstractCollectionDataProvider;
use AppBundle\Entity\DealOfferPrice;
use AppBundle\Repository\DealOfferPriceRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Возвращает массив цен купонов, у которых нет привязки к номерам отелей
 *
 * @package AppBundle\DataProvider
 */
final class DealOfferPricesWithoutRoomsCollectionDataProvider extends AbstractCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
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
            $resourceClass === DealOfferPrice::class
            && $operationName === 'get_deal_offer_prices_without_rooms';
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

        /** @var DealOfferPriceRepository $dealOfferPriceRepository */
        $dealOfferPriceRepository = $this->managerRegistry->getManager()->getRepository($resourceClass);

        $baseQuery = $dealOfferPriceRepository->getDealOfferPricesWithoutRoomsQuery('dop', $searchQuery);
        $baseQuery->select('dop.id');
        $ids = array_map('current', $baseQuery->getQuery()->getResult());

        return $dealOfferPriceRepository->createQueryBuilder('dop')
            ->andWhere('dop.id in (:ids)')
            ->setParameter('ids', $ids);
    }
}
