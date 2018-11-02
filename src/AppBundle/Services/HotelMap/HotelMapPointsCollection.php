<?php

namespace AppBundle\Services\HotelMap;

use AppBundle\Dto\HotelMap\HotelMapPointDto;

/**
 * Класс, содержащий данные для отрисовки карты отелей на фронте
 *
 * Class HotelMap
 *
 * @package AppBundle\Services\HotelMap
 */
class HotelMapPointsCollection implements \JsonSerializable
{
    public const DEAL_OFFER_HOTELS_CATEGORY = 61;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @param HotelMapPointDto $pointDto
     * @param string|null      $photoUrl
     * @return $this
     */
    public function addPointWithPhoto(HotelMapPointDto $pointDto, string $photoUrl = null): self
    {
        $elKey = $pointDto->dealOfferId;

        $el = [];
        $el['id'] = $pointDto->dealOfferId;
        $el['administrativeArea'] = $pointDto->administrativeArea;
        $el['hotelId'] = $pointDto->hotelId;
        $el['hotelPhoto'] = $photoUrl;
        $el['sysname'] = $pointDto->sysname;
        $el['hotelTitle'] = $pointDto->hotelTitle;
        $el['titleLink'] = $pointDto->titleLink;
        $el['dealOfferTitle'] = $pointDto->dealOfferTitle;
        $el['rating'] = $pointDto->rating;
        $el['salesCount'] = $pointDto->salesCount;
        $el['locality'] = $pointDto->locality;
        $el['minPrice'] = $pointDto->minPrice;
        $el['minOriginalPrice'] = $pointDto->minOriginalPrice;
        $el['discount'] = $pointDto->discount;
        $el['dealOfferCategory'] = self::DEAL_OFFER_HOTELS_CATEGORY;
        $el['isStub'] = false;
        $el['locations'] = [
            [
                'coordinates' => [
                    'latitude' => $pointDto->latitude,
                    'longitude' => $pointDto->longitude,
                ],
            ],
        ];

        $this->data[$elKey] = $el;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        if (!$this->data) {
            return (object)$this->data;
        }

        return $this->data;
    }
}
