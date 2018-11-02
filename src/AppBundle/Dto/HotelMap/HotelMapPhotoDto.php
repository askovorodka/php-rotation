<?php

namespace AppBundle\Dto\HotelMap;

class HotelMapPhotoDto
{
    /**
     * @var int
     */
    public $hotelId;

    /**
     * @var int
     */
    public $photoId;

    /**
     * @var int
     */
    public $areaWidth;

    /**
     * @var int
     */
    public $areaHeight;

    /**
     * @var int
     */
    public $offsetTop;

    /**
     * @var int
     */
    public $offsetLeft;

    /**
     * @var string
     */
    public $photoFile;
}
