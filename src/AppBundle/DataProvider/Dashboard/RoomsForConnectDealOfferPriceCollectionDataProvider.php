<?php

namespace AppBundle\DataProvider\Dashboard;

use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use AppBundle\DataProvider\AbstractCollectionDataProvider;
use AppBundle\Entity\DealOfferPrice;
use AppBundle\Entity\Room;
use AppBundle\Repository\DealOfferPriceRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Возвращает массив комнат, доступных для привязке к указанной цене купона
 *
 * @package AppBundle\DataProvider
 */
final class RoomsForConnectDealOfferPriceCollectionDataProvider extends AbstractCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var RequestStack */
    protected $requestStack;

    /**
     * @inheritdoc
     *
     * @param RequestStack $requestStack
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        array $collectionExtensions = [],
        RequestStack $requestStack
    ) {
        $this->requestStack = $requestStack;

        parent::__construct($managerRegistry, $collectionExtensions);
    }

    /**
     * @inheritdoc
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return
            $resourceClass === Room::class
            && $operationName === 'get_rooms_for_connect_deal_offer_price';
    }

    /**
     * @inheritdoc
     */
    protected function createQueryBuilder(string $resourceClass, string $operationName = null, array $context = [])
    {
        if (!(($dealOfferPriceId = (int)$this->requestStack->getCurrentRequest()->get('dealOfferPriceId')) > 0)) {
            throw new \InvalidArgumentException('dealOfferPriceId GET-parameter required');
        }

        /** @var DealOfferPriceRepository $dealOfferPriceRepository */
        $dealOfferPriceRepository = $this->managerRegistry->getManager()->getRepository(DealOfferPrice::class);

        /** @var DealOfferPrice $dealOfferPrice */
        if (!($dealOfferPrice = $dealOfferPriceRepository->find(['id' => $dealOfferPriceId]))) {
            throw new NotFoundHttpException("deal-offer-price specified by `id`={$dealOfferPriceId} was not found");
        }

        /** @var EntityRepository $roomsRepository */
        $roomsRepository = $this->managerRegistry->getManagerForClass($resourceClass)->getRepository($resourceClass);

        if (($hotel = $dealOfferPrice->getDealOffer()->getHotel())) {
            $queryBuilder = $roomsRepository->createQueryBuilder('r');
            $queryBuilder->andWhere($queryBuilder->expr()->eq('r.hotel', $hotel->getId()));

            return $queryBuilder;
        }

        return null;
    }
}
