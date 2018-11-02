<?php

namespace AppBundle\Services;

use AppBundle\Entity\HotelPhoto;
use AppBundle\Entity\RoomPhoto;

final class PhotoUrlService
{
    public const DEFAULT_WIDTH = 672;
    public const DEFAULT_HEIGHT = 378;

    /** @var string $photosLinkPrefix */
    private $photosLinkPrefix;

    /**
     * PhotoUrlService constructor.
     *
     * @param string $photosLinkPrefix
     */
    public function __construct(string $photosLinkPrefix)
    {
        $this->photosLinkPrefix = $photosLinkPrefix;
    }

    /**
     * method generate full url on photo with crop and resize parameters
     *
     * @param HotelPhoto|RoomPhoto $photoEntity
     * @param int                  $width
     * @param int                  $height
     * @return string
     */
    public function getPhotoUrl($photoEntity, int $width = self::DEFAULT_WIDTH, int $height = self::DEFAULT_HEIGHT): string
    {
        if (
            !$photoEntity instanceof HotelPhoto &&
            !$photoEntity instanceof RoomPhoto
        ) {
            throw new \InvalidArgumentException('$photoEntity must be instance of HotelPhoto or RoomPhoto');
        }

        return $this->getPhotoUrlByMetadata(
            $photoEntity->getAreaWidth(),
            $photoEntity->getAreaHeight(),
            $photoEntity->getOffsetTop(),
            $photoEntity->getOffsetLeft(),
            $photoEntity->getPhoto()->getPhoto(),
            $width,
            $height
        );
    }

    /**
     * @param int    $areaWidth
     * @param int    $areaHeight
     * @param int    $offsetTop
     * @param int    $offsetLeft
     * @param string $photoFile
     * @param int    $width
     * @param int    $height
     * @return string
     */
    public function getPhotoUrlByMetadata(
        int $areaWidth,
        int $areaHeight,
        int $offsetTop,
        int $offsetLeft,
        string $photoFile,
        int $width = self::DEFAULT_WIDTH,
        int $height = self::DEFAULT_HEIGHT
    ): string {
        $photoUrl = sprintf('/%s/%s/%s/%s/%s/%s/hotels/%s',
            $width,
            $height,
            $areaWidth,
            $areaHeight,
            $offsetTop,
            $offsetLeft,
            $photoFile
        );

        return $this->photosLinkPrefix . $photoUrl;
    }
}
