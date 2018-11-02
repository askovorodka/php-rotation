<?php

namespace AppBundle\Validator\Hotel;

use AppBundle\Entity\Hotel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class HotelIsProductionValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value) {
            return;
        }

        $hotel = $this->context->getObject();

        if (!$hotel instanceof Hotel) {
            throw new \RuntimeException('HotelIsProduction constraint can be use only for Hotel entity');
        }

        $now = new \DateTime();

        // Проверяем наличие активной фотографии
        $activePhotoNotFound = true;
        foreach ($hotel->getHotelPhotos() as $hotelPhoto) {
            if ($hotelPhoto->getIsActive() && $hotelPhoto->getPhoto()->getIsActive()) {
                $activePhotoNotFound = false;
                break;
            }
        }
        if ($activePhotoNotFound) {
            $this->context->buildViolation('У отеля отсутствуют активные фотографии')->addViolation();
        }

        // Проверяем наличие активной комнаты c активной ценой
        $activeRoomNotFound = true;
        foreach ($hotel->getRooms() as $room) {
            if ($room->getIsActive()) {
                foreach ($room->getDealOfferRoomPrices() as $roomPrice) {
                    $price = $roomPrice->getDealOfferPrice();
                    if ($price->getValidDate() > $now) {
                        $activeRoomNotFound = false;
                        break 2;
                    }
                }
            }
        }
        if ($activeRoomNotFound) {
            $this->context->buildViolation('У отеля нет активных комнат с активной ценой')->addViolation();
        }

        // Проверяем наличие активного удобства
        $activeAmenityNotFound = true;
        foreach ($hotel->getHotelAmenities() as $hotelAmenity) {
            if ($hotelAmenity->getIsActive() && $hotelAmenity->getAmenity()->getIsActive()) {
                $activeAmenityNotFound = false;
                break;
            }
        }
        if ($activeAmenityNotFound) {
            $this->context->buildViolation('У отеля нет активных удобств')->addViolation();
        }

        // Проверяем наличие валидной акции
        $activeDealOfferNotFound = true;
        foreach ($hotel->getDealOffers() as $dealOffer) {
            if ($dealOffer->getIsActive() && $dealOffer->getValidAt() > $now) {
                $activeDealOfferNotFound = false;
                break;
            }
        }
        if ($activeDealOfferNotFound) {
            $this->context->buildViolation('У отеля нет активной акции')->addViolation();
        }
    }
}
