<?php

namespace AppBundle\DataProvider\Admin;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use AppBundle\Entity\DealOffer;
use AppBundle\Entity\DealOfferPrice;
use AppBundle\Entity\Hotel;
use Doctrine\ORM\EntityManager;

final class HotelDealOfferPriceItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var EntityManager $em */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Hotel::class && $operationName === 'get_hotel_deal_offers_prices';
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var Hotel $hotelEntity */
        $hotelEntity = $this->em->getRepository(Hotel::class)->find($id);
        if ($hotelEntity) {
            /** @var DealOffer $dealOffer */
            foreach ($hotelEntity->getDealOffers() as $dealOffer) {
                $this->removeDealOfferPricesWithoutRooms($dealOffer);
            }
        }

        return $hotelEntity;
    }

    /**
     * @param DealOffer $dealOffer
     */
    private function removeDealOfferPricesWithoutRooms(DealOffer $dealOffer): void
    {
        /** @var DealOfferPrice $dealOfferPrice */
        foreach ($dealOffer->getDealOfferPrices() as $dealOfferPrice) {
            if (!$dealOfferPrice->getDealOfferRooms()->count()) {
                $dealOffer->removeDealOfferPrice($dealOfferPrice);
            }
        }
    }
}
