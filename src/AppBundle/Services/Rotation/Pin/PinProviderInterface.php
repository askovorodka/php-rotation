<?php

namespace AppBundle\Services\Rotation\Pin;

/**
 * Interface PinProviderInterface
 *
 * @package AppBundle\Services\Pin
 */
interface PinProviderInterface
{
    /**
     * @param int $cityId
     * @param int $regionId
     * @param int $presetId
     * @return PinCollection
     */
    public function getPins(?int $cityId, ?int $regionId, int $presetId): PinCollection;
}
