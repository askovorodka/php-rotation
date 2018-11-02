<?php

namespace AppBundle\DataProvider\HotelView;

use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use AppBundle\Entity\Hotel;
use AppBundle\Services\HotelsSearch\Exceptions\HotelNotFoundException;
use AppBundle\Services\HotelsSearch\HotelsSearchServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Возвращает данные для отображения страницы отеля (на стороне монолита)
 */
final class HotelViewItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var HotelsSearchServiceInterface
     */
    private $hotelsSearchService;

    /**
     * HotelViewItemDataProvider constructor.
     *
     * @param EntityManagerInterface       $em
     * @param HotelsSearchServiceInterface $hotelsSearchService
     */
    public function __construct(
        EntityManagerInterface $em,
        HotelsSearchServiceInterface $hotelsSearchService
    ) {
        $this->em = $em;
        $this->hotelsSearchService = $hotelsSearchService;
    }

    /**
     * @inheritdoc
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return
            $resourceClass === Hotel::class
            && $operationName === 'get_hotel_view';
    }

    /**
     * @inheritdoc
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $sysname = $id;

        try {
            $foundHotel = $this->hotelsSearchService->findBySysname($sysname);
        } catch (HotelNotFoundException $e) {
            throw new NotFoundHttpException("Отель с `sysname` = $sysname не найден");
        }

        $hotelRepository = $this->em->getRepository(Hotel::class);

        /** @var Hotel $hotel */
        $hotel = $hotelRepository->find($foundHotel->getHotelId());
        $foundHotel->fillHotelMinPrice($hotel);

        // Оставляем только те цены и комнаты, которые продаются по активной акции
        $now = new \DateTime();

        foreach ($hotel->getRooms() as $room) {
            foreach ($room->getDealOfferRoomPrices() as $dealOfferRoomPrice) {
                $dealOfferPrice = $dealOfferRoomPrice->getDealOfferPrice();

                if ($dealOfferPrice->getDealOffer()->getId() !== $hotel->getActiveDealOffer()->getId()) {
                    $room->removeDealOfferRoomPrice($dealOfferRoomPrice);
                } elseif (
                    $dealOfferPrice->getValidDate() &&
                    $now > $dealOfferPrice->getValidDate()
                ) {
                    $room->removeDealOfferRoomPrice($dealOfferRoomPrice);
                } elseif ($dealOfferPrice->getMaxCoupone() < 0) {
                    $room->removeDealOfferRoomPrice($dealOfferRoomPrice);
                }
            }

            if (!count($room->getDealOfferRoomPrices())) {
                $hotel->removeRoom($room);
            }

            // Оставляем только активные удобства
            foreach ($room->getRoomAmenities() as $roomAmenity) {
                if (!($roomAmenity->getIsActive() && $roomAmenity->getAmenity()->getIsActive())) {
                    $room->removeRoomAmenity($roomAmenity);
                }
            }

            // Оставляем только активные фотографии
            foreach ($room->getRoomPhotos() as $roomPhoto) {
                if (!($roomPhoto->getIsActive() && $roomPhoto->getPhoto()->getIsActive())) {
                    $room->removeRoomPhoto($roomPhoto);
                }
            }
        }

        // Оставляем только активные удобства и фотографии
        foreach ($hotel->getHotelAmenities() as $hotelAmenity) {
            if (!($hotelAmenity->getIsActive() && $hotelAmenity->getAmenity()->getIsActive())) {
                $hotel->removeHotelAmenity($hotelAmenity);
            }
        }
        foreach ($hotel->getHotelPhotos() as $hotelPhoto) {
            if (!($hotelPhoto->getIsActive() && $hotelPhoto->getPhoto()->getIsActive())) {
                $hotel->removeHotelPhoto($hotelPhoto);
            }
        }

        return $hotel;
    }
}
