<?php

namespace AppBundle\Services\Rotation\Dto;

/**
 * Class HotelDto
 *
 * @package AppBundle\Services\Rotation\Dto
 */
class HotelDto
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $sysname;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $administrativeArea;

    /**
     * @var float|null
     */
    public $rating;

    /**
     * @var int
     */
    public $minPrice;

    /**
     * @var int
     */
    public $minOriginalPrice;

    /**
     * @var int
     */
    public $discount;

    /**
     * @var DealOfferDto
     */
    public $dealOffer;

    /**
     * HotelDto constructor.
     *
     * @param int          $id
     * @param string       $sysname
     * @param string       $title
     * @param string       $administrativeArea
     * @param float|null   $rating
     * @param int          $minPrice
     * @param int          $minOriginalPrice
     * @param int          $discount
     * @param DealOfferDto $dealOffer
     */
    public function __construct(
        int $id,
        string $sysname,
        string $title,
        string $administrativeArea,
        ?float $rating,
        int $minPrice,
        int $minOriginalPrice,
        int $discount,
        DealOfferDto $dealOffer
    ) {
        $this->id = $id;
        $this->sysname = $sysname;
        $this->title = $title;
        $this->administrativeArea = $administrativeArea;
        $this->rating = $rating;
        $this->minPrice = $minPrice;
        $this->minOriginalPrice = $minOriginalPrice;
        $this->discount = $discount;
        $this->dealOffer = $dealOffer;
    }

}
