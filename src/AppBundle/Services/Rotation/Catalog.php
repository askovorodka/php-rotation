<?php

namespace AppBundle\Services\Rotation;

use AppBundle\Services\Rotation\Dto\HotelDto;
use AppBundle\Services\Rotation\Dto\PresetDto;

/**
 * Class Catalog
 *
 * @package AppBundle\Services\Rotation
 */
class Catalog
{
    private $banners = [];

    private $items = [];

    private $catalogHotelCache = [];

    private $catalogPresetCache = [];

    private $bannerHotelCache = [];

    private $bannerPresetCache = [];

    /**
     * @param HotelDto $hotel
     * @return bool
     */
    public function addHotelToCatalogIfNotExists(HotelDto $hotel): bool
    {
        if (isset($this->catalogHotelCache[$hotel->id])) {
            return false;
        }

        $this->items[] = $hotel;
        $this->catalogHotelCache[$hotel->id] = true;

        return true;
    }

    /**
     * @param PresetDto $preset
     * @return bool
     */
    public function addPresetToCatalogIfNotExists(PresetDto $preset): bool
    {
        if (isset($this->catalogPresetCache[$preset->id])) {
            return false;
        }

        $this->items[] = $preset;
        $this->catalogHotelCache[$preset->id] = true;

        return true;
    }

    /**
     * @param HotelDto $hotel
     * @return bool
     */
    public function addHotelToBannersIfNotExists(HotelDto $hotel): bool
    {
        if (isset($this->bannerHotelCache[$hotel->id])) {
            return false;
        }

        $this->banners[] = $hotel;
        $this->bannerHotelCache[$hotel->id] = true;

        return true;
    }

    /**
     * @param PresetDto $preset
     * @return bool
     */
    public function addPresetToBannersIfNotExists(PresetDto $preset): bool
    {
        if (isset($this->bannerPresetCache[$preset->id])) {
            return false;
        }

        $this->banners[] = $preset;
        $this->bannerPresetCache[$preset->id] = true;

        return true;
    }

    /**
     * @return array
     */
    public function getBanners(): array
    {
        return $this->banners;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

}
