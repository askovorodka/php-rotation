<?php

namespace AppBundle\DataProvider\DealOffer;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use AppBundle\DataPreparer\ActiveHotelPreparer;
use AppBundle\Entity\Amenity;
use AppBundle\Entity\DealOffer;
use AppBundle\Entity\Hotel;
use AppBundle\Entity\HotelAmenity;
use AppBundle\Entity\HotelPhoto;
use AppBundle\Entity\Room;
use AppBundle\Helper\MobileRequestHelper;
use AppBundle\Services\HotelsSearch\HotelsSearchServiceInterface;
use AppBundle\Services\PhotoUrlService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DealOfferMobileItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var HotelsSearchServiceInterface $hotelSearchService
     */
    private $hotelSearchService;

    /**
     * @var ActiveHotelPreparer
     */
    private $activeHotelPreparer;

    /**
     * @var PhotoUrlService $photoUrlService
     */
    private $photoUrlService;

    /**
     * @var string $iconLinkPrefix
     */
    private $iconsLinkPrefix;

    public function __construct(
        EntityManagerInterface $em,
        HotelsSearchServiceInterface $hotelsSearchService,
        ActiveHotelPreparer $activeHotelPreparer,
        PhotoUrlService $photoUrlService,
        string $iconsLinkPrefix
    ) {
        $this->em = $em;
        $this->hotelSearchService = $hotelsSearchService;
        $this->activeHotelPreparer = $activeHotelPreparer;
        $this->photoUrlService = $photoUrlService;
        $this->iconsLinkPrefix = $iconsLinkPrefix;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === DealOffer::class &&
            in_array($operationName, [
                'get_hotel_view_by_deal_offer_mobile',
                'get_hotel_by_deal_offer_mobile',
            ]);
    }

    /**
     * @param string      $resourceClass
     * @param int|string  $id
     * @param string|null $operationName
     * @param array       $context
     * @return DealOffer|null
     * @throws \AppBundle\Services\HotelsSearch\Exceptions\HotelNotFoundException
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var DealOffer $dealOffer */
        $dealOffer = $this->em->getRepository(DealOffer::class)->find($id);

        if (!$dealOffer) {
            return null;
        }

        $now = new \DateTime();
        if (!$dealOffer->getIsActive() || $dealOffer->getValidAt() < $now) {
            throw new NotFoundHttpException();
        }

        /** @var Hotel $hotel */
        $hotel = $dealOffer->getHotel();
        if (!$hotel) {
            throw new NotFoundHttpException();
        }

        $this->activeHotelPreparer->prepare($hotel);

        foreach ($hotel->getHotelAmenities()->getIterator() as $hotelAmenity) {
            $this->prepareHotelAmenity($hotel, $hotelAmenity);
        }

        /** @var Room $room */
        foreach ($hotel->getRooms() as $room) {
            $this->prepareRoom($hotel, $room);
        }

        //refresh rooms
        $hotel->setRooms(new ArrayCollection($hotel->getRooms()->getValues()));

        //refresh amenities
        $hotel->setHotelAmenities(new ArrayCollection($hotel->getHotelAmenities()->getValues()));

        $this->setHotelPhotosUrls($hotel);

        //find min price for hotel
        $foundHotel = $this->hotelSearchService->findById($hotel->getId());
        $foundHotel->fillHotelMinPrice($hotel);

        return $dealOffer;
    }

    /**
     * method prepare Room of Hotel
     *
     * @param Hotel $hotel
     * @param Room  $room
     * @return void
     */
    private function prepareRoom(Hotel $hotel, Room $room): void
    {
        $this->prepareRoomPrice($hotel, $room);
        $this->prepareRoomAmenities($room);
        $this->setRoomPhotos($room);
    }

    /**
     * method add photo url in HotelPhoto response
     *
     * @param Hotel $hotel
     */
    private function setHotelPhotosUrls(Hotel $hotel)
    {
        if ($hotelPhotos = $hotel->getHotelPhotos()) {
            /** @var HotelPhoto $hotelPhoto */
            foreach ($hotelPhotos as $hotelPhoto) {
                $hotelPhoto->setUrl($this->photoUrlService->getPhotoUrl($hotelPhoto));
                $hotel->addPhotoUrl($this->photoUrlService->getPhotoUrl($hotelPhoto));
            }
        }
    }

    /**
     * method set full url on room photos
     *
     * @param Room $room
     */
    private function setRoomPhotos(Room $room)
    {
        if ($roomPhotos = $room->getRoomPhotos()) {
            foreach ($roomPhotos as $roomPhoto) {
                $photo = $roomPhoto->getPhoto()->getPhoto();

                if (!$photo) {
                    continue;
                }

                if (strpos($photo, "http")) {
                    continue;
                }

                $photo = $this->photoUrlService->getPhotoUrl($roomPhoto);
                $roomPhoto->getPhoto()->setPhoto($photo);
                $room->addPhotoUrl($photo);
            }
        }
    }

    /**
     * method set amenities icons on rooms
     *
     * @param Room $room
     */
    private function prepareRoomAmenities(Room $room)
    {
        foreach ($room->getRoomAmenities() as $roomAmenity) {
            $amenity = $roomAmenity->getAmenity();

            if (!$roomAmenity->getIsActive() || !$amenity->getIsActive()) {
                $room->removeRoomAmenity($roomAmenity);
            }

            $icon = $amenity->getIcon();
            if (!$icon) {
                continue;
            }

            if (strstr($icon, "http")) {
                continue;
            }

            $icon = MobileRequestHelper::changePngExtention($icon);
            $roomAmenity->getAmenity()->setIcon($this->iconsLinkPrefix . $icon);
            $roomAmenity->getAmenity()->setPreviewsSmall($this->iconsLinkPrefix . 'w48-' . $icon);
        }

        //refresh roomAmenities collection
        $room->setRoomAmenities(new ArrayCollection($room->getRoomAmenities()->getValues()));
    }

    /**
     * method remove rooms includes empty deal_offer_price
     *
     * @param Hotel $hotel
     * @param Room  $room
     * @return void
     */
    private function prepareRoomPrice(Hotel $hotel, Room $room): void
    {
        $now = new \DateTime();

        if (!$room->getDealOfferRoomPrices()->count()) {
            $hotel->removeRoom($room);
        }

        foreach ($room->getDealOfferRoomPrices() as $dealOfferRoomPrice) {
            $dealOfferPrice = $dealOfferRoomPrice->getDealOfferPrice();

            if ($dealOfferPrice->getDealOffer()->getId() !== $hotel->getActiveDealOffer()->getId()) {
                $room->removeDealOfferRoomPrice($dealOfferRoomPrice);
            } elseif ($dealOfferPrice->getValidDate() && $now > $dealOfferPrice->getValidDate()) {
                $room->removeDealOfferRoomPrice($dealOfferRoomPrice);
            } elseif ($dealOfferPrice->getMaxCoupone() < 0) {
                $room->removeDealOfferRoomPrice($dealOfferRoomPrice);
            } else {
                $room->addDealOfferPrice($dealOfferPrice);
            }
        }
    }

    /**
     * @param Hotel        $hotel
     * @param HotelAmenity $hotelAmenity
     */
    private function prepareHotelAmenity(Hotel $hotel, HotelAmenity $hotelAmenity)
    {
        /** @var Amenity $amenity */
        $amenity = $hotelAmenity->getAmenity();

        //только активные Amenity (is_active=1)
        if (!$amenity->getIsActive()) {
            $hotel->removeHotelAmenity($hotelAmenity);
        }

        $icon = $amenity->getIcon();
        if (!$icon) {
            return;
        }

        if (strstr($icon, "http")) {
            return;
        }

        $icon = MobileRequestHelper::changePngExtention($icon);
        $hotelAmenity->getAmenity()->setIcon($this->iconsLinkPrefix . $icon);
        $hotelAmenity->getAmenity()->setPreviewsSmall($this->iconsLinkPrefix . 'w48-' . $icon);
    }

}
