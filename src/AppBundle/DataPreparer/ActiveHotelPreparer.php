<?php

namespace AppBundle\DataPreparer;

use AppBundle\Entity\Hotel;

class ActiveHotelPreparer
{
    public function prepare(Hotel $hotel)
    {
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
    }
}
